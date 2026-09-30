<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $roleFilter = (string) $request->query('role', '');

        $users = User::query()
            ->with(['roles:id,name,guard_name', 'permissions:id,name,guard_name'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            }))
            ->when($roleFilter !== '', fn ($query) => $query->whereHas('roles', fn ($roles) => $roles->where('roles.name', $roleFilter)->where('roles.guard_name', 'web')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'matric_no' => $user->matric_no,
                'roles' => $user->roles->pluck('name')->values(),
                'permissions' => $user->permissions->pluck('name')->values(),
                'email_verified' => $user->email_verified_at !== null,
                'created_at' => $user->created_at?->toDateString(),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => Role::query()->where('guard_name', 'web')->orderBy('name')->pluck('name'),
            'permissions' => Permission::query()->where('guard_name', 'web')->orderBy('name')->pluck('name'),
            'filters' => ['search' => $search, 'role' => $roleFilter],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeUserInput($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'matric_no' => ['nullable', 'string', 'max:50', 'unique:users,matric_no'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'distinct', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'matric_no' => $validated['matric_no'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);
        $user->syncRoles($validated['roles']);
        $user->syncPermissions($validated['permissions'] ?? []);

        return to_route('admin.users.index');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->normalizeUserInput($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'matric_no' => ['nullable', 'string', 'max:50', Rule::unique('users', 'matric_no')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'matric_no' => $validated['matric_no'] ?? null,
        ]);

        return back();
    }

    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'distinct', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ]);

        if ($user->is($request->user()) && $user->can('manage users') && ! $this->rolesCanManageUsers($validated['roles']) && ! $user->hasDirectPermission('manage users')) {
            throw ValidationException::withMessages(['roles' => 'Keep a role or direct permission with user-management access on your account.']);
        }

        $user->syncRoles($validated['roles']);

        return back();
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        $roles = $user->roles()->pluck('name')->all();
        if ($user->is($request->user()) && $user->can('manage users') && ! $this->rolesCanManageUsers($roles) && ! in_array('manage users', $validated['permissions'], true)) {
            throw ValidationException::withMessages(['permissions' => 'Keep a role or direct permission with user-management access on your account.']);
        }

        $user->syncPermissions($validated['permissions']);

        return back();
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            throw ValidationException::withMessages(['user' => 'You cannot delete your own account here.']);
        }

        if ($user->can('manage users') && ! $this->hasOtherUserManager($user)) {
            throw ValidationException::withMessages(['user' => 'The last user manager cannot be deleted.']);
        }

        $user->delete();

        return back();
    }

    private function normalizeUserInput(Request $request): void
    {
        $normalized = [];

        if ($request->exists('name')) {
            $normalized['name'] = trim((string) $request->input('name'));
        }
        if ($request->exists('email')) {
            $normalized['email'] = strtolower(trim((string) $request->input('email')));
        }
        if ($request->exists('matric_no')) {
            $normalized['matric_no'] = $request->filled('matric_no') ? strtoupper(trim((string) $request->input('matric_no'))) : null;
        }

        $request->merge($normalized);
    }

    private function rolesCanManageUsers(array $roles): bool
    {
        return Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $roles)
            ->whereHas('permissions', fn ($query) => $query->where('name', 'manage users')->where('guard_name', 'web'))
            ->exists();
    }

    private function hasOtherUserManager(User $user): bool
    {
        return User::query()
            ->where('id', '!=', $user->id)
            ->where(function ($query) {
                $query->whereHas('roles.permissions', fn ($permissions) => $permissions->where('permissions.name', 'manage users')->where('permissions.guard_name', 'web'))
                    ->orWhereHas('permissions', fn ($permissions) => $permissions->where('permissions.name', 'manage users')->where('permissions.guard_name', 'web'));
            })
            ->exists();
    }
}