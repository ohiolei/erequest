<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ACLController extends Controller
{
    public function __construct(protected ActivityService $activities) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::query()
                ->where('guard_name', 'web')
                ->with('permissions')
                ->withCount('users')
                ->orderBy('name')
                ->paginate(10, ['*'], 'roles_page')
                ->withQueryString()
                ->through(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->values(),
                    'users_count' => $role->users_count,
                ]),
            'permissions' => Permission::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->pluck('name'),
            'permissionRecords' => Permission::query()
                ->where('guard_name', 'web')
                ->withCount(['roles', 'users'])
                ->orderBy('name')
                ->paginate(10, ['*'], 'permissions_page')
                ->withQueryString()
                ->through(fn (Permission $permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'roles_count' => $permission->roles_count,
                    'users_count' => $permission->users_count,
                ]),
        ]);
    }

    public function storePermission(): never
    {
        abort(403, 'Permissions are read-only.');
    }

    public function updatePermission(Permission $permission): never
    {
        abort(403, 'Permissions are read-only.');
    }

    public function destroyPermission(Permission $permission): never
    {
        abort(403, 'Permissions are read-only.');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['name' => strtolower(trim((string) $request->input('name')))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        $role->syncPermissions($validated['permissions'] ?? []);

        $this->activities->log('created_role', 'Created role', Role::class, $role->id, [
            'name' => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return back();
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'web', 404);

        $request->merge(['name' => strtolower(trim((string) $request->input('name')))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($role->id)],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        if ($role->name === 'admin' && $validated['name'] !== 'admin') {
            throw ValidationException::withMessages(['name' => 'The admin role cannot be renamed.']);
        }

        $permissions = $validated['permissions'] ?? [];
        if ($role->name === 'admin') {
            $permissions[] = 'manage roles';
        }

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions(array_unique($permissions));

        $this->activities->log('updated_role', 'Updated role', Role::class, $role->id, [
            'name' => $validated['name'],
            'permissions' => array_unique($permissions),
        ]);

        return back();
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'admin') {
            throw ValidationException::withMessages(['role' => 'The admin role cannot be deleted.']);
        }

        if ($role->users()->exists()) {
            throw ValidationException::withMessages(['role' => 'Roles assigned to users cannot be deleted.']);
        }

        $role->delete();

        $this->activities->log('deleted_role', 'Deleted role', Role::class, $role->id, [
            'name' => $role->name,
        ]);

        return back();
    }
}
