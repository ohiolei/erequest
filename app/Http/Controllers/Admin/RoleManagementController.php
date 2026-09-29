<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::query()
                ->where('guard_name', 'web')
                ->with('permissions')
                ->withCount('users')
                ->orderBy('name')
                ->get()
                ->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->values(),
                    'users_count' => $role->users_count,
                ])
                ->values(),
            'permissions' => Permission::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->pluck('name'),
            'permissionRecords' => Permission::query()
                ->where('guard_name', 'web')
                ->withCount(['roles', 'users'])
                ->orderBy('name')
                ->get()
                ->map(fn (Permission $permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'roles_count' => $permission->roles_count,
                    'users_count' => $permission->users_count,
                ])
                ->values(),
        ]);
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $request->merge(['name' => strtolower(trim((string) $request->input('name')))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?: [a-z0-9]+)*$/', Rule::unique('permissions', 'name')->where('guard_name', 'web')],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return back();
    }

    public function updatePermission(Request $request, Permission $permission): RedirectResponse
    {
        abort_unless($permission->guard_name === 'web', 404);

        $request->merge(['name' => strtolower(trim((string) $request->input('name')))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?: [a-z0-9]+)*$/', Rule::unique('permissions', 'name')->where('guard_name', 'web')->ignore($permission->id)],
        ]);

        if ($permission->name === 'manage roles' && $validated['name'] !== 'manage roles') {
            throw ValidationException::withMessages(['name' => 'The manage roles permission cannot be renamed.']);
        }

        $permission->update(['name' => $validated['name']]);

        return back();
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        abort_unless($permission->guard_name === 'web', 404);

        if ($permission->name === 'manage roles') {
            throw ValidationException::withMessages(['permission' => 'The manage roles permission cannot be deleted.']);
        }

        if ($permission->roles()->exists() || $permission->users()->exists()) {
            throw ValidationException::withMessages(['permission' => 'Remove this permission from roles and users before deleting it.']);
        }

        $permission->delete();

        return back();
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

        return back();
    }
}
