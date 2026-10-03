<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;
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
    public function __construct(protected ActivityService $activities) {}

    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $roleFilter = (string) $request->query('role', '');
        $tab = (string) $request->query('tab', 'students');

        $users = User::query()
            ->with(['roles:id,name,guard_name', 'permissions:id,name,guard_name'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('email', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%")
                    ->orWhere('staff_number', 'like', "%{$search}%");

                foreach (preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) as $term) {
                    $query->orWhere('fname', 'like', "%{$term}%")
                        ->orWhere('mname', 'like', "%{$term}%")
                        ->orWhere('lname', 'like', "%{$term}%");
                }
            }))
            ->when($roleFilter !== '', fn ($query) => $query->whereHas('roles', fn ($roles) => $roles->where('roles.name', $roleFilter)->where('roles.guard_name', 'web')))
            ->when($tab === 'students', fn ($query) => $query->whereHas('roles', fn ($roles) => $roles->where('name', 'student')->where('guard_name', 'web')))
            ->when($tab === 'staff', fn ($query) => $query->whereDoesntHave('roles', fn ($roles) => $roles->where('name', 'student')->where('guard_name', 'web')))
            ->orderBy('fname')
            ->orderBy('lname')
            ->paginate(10, ['*'], 'users_page')
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'fname' => $user->fname,
                'mname' => $user->mname,
                'lname' => $user->lname,
                'email' => $user->email,
                'matric_no' => $user->matric_no,
                'staff_number' => $user->staff_number,
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
            'tab' => $tab,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeUserInput($request);

        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:100'],
            'mname' => ['nullable', 'string', 'max:100'],
            'lname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'matric_no' => ['nullable', 'string', 'max:50', 'unique:users,matric_no'],
            'staff_number' => ['nullable', 'string', 'max:50', 'unique:users,staff_number'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'distinct', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        $request->user()->can('create staff') || abort(403);

        if (in_array('student', $validated['roles'], true)) {
            throw ValidationException::withMessages(['roles' => 'Student accounts cannot be created from here.']);
        }

        $user = User::create([
            'fname' => trim($validated['fname']),
            'mname' => filled($validated['mname'] ?? null) ? trim($validated['mname']) : null,
            'lname' => trim($validated['lname']),
            'email' => $validated['email'],
            'matric_no' => $validated['matric_no'] ?? null,
            'staff_number' => $validated['staff_number'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);
        $user->syncRoles($validated['roles']);
        $user->syncPermissions($validated['permissions'] ?? []);

        $this->activities->log('created_user', 'Created user account', User::class, $user->id, [
            'email' => $user->email,
            'roles' => $validated['roles'],
        ]);

        return to_route('admin.users.index');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->normalizeUserInput($request);

        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:100'],
            'mname' => ['nullable', 'string', 'max:100'],
            'lname' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'matric_no' => ['nullable', 'string', 'max:50', Rule::unique('users', 'matric_no')->ignore($user->id)],
            'staff_number' => ['nullable', 'string', 'max:50', Rule::unique('users', 'staff_number')->ignore($user->id)],
        ]);

        if ($user->hasRole('student') && ! $request->user()->can('edit students')) {
            throw ValidationException::withMessages(['user' => 'You do not have permission to edit student accounts.']);
        }

        $user->update([
            'fname' => trim($validated['fname']),
            'mname' => filled($validated['mname'] ?? null) ? trim($validated['mname']) : null,
            'lname' => trim($validated['lname']),
            'email' => $validated['email'],
            'matric_no' => $validated['matric_no'] ?? null,
            'staff_number' => $validated['staff_number'] ?? null,
        ]);

        $this->activities->log('updated_user', 'Updated user profile', User::class, $user->id, [
            'email' => $user->email,
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

        $this->activities->log('updated_user_roles', 'Updated user roles', User::class, $user->id, [
            'roles' => $validated['roles'],
        ]);

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

        $this->activities->log('updated_user_permissions', 'Updated user direct permissions', User::class, $user->id, [
            'permissions' => $validated['permissions'],
        ]);

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

        $this->activities->log('deleted_user', 'Deleted user account', User::class, $user->id, [
            'email' => $user->email,
        ]);

        return back();
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            throw ValidationException::withMessages(['user' => 'You cannot reset your own password from here.']);
        }

        if ($user->can('manage users') && ! $this->hasOtherUserManager($user)) {
            throw ValidationException::withMessages(['user' => 'The last user manager cannot have their password reset.']);
        }

        $newPassword = $user->lname ?: 'password';

        $user->password = Hash::make($newPassword);
        $user->save();

        $this->activities->log('reset_user_password', 'Reset user password', User::class, $user->id, [
            'email' => $user->email,
        ]);

        return back()->with('status', "Password reset to: {$newPassword}");
    }

    public function resetTwoFactor(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            throw ValidationException::withMessages(['user' => 'You cannot reset your own 2FA from here.']);
        }

        if ($user->can('manage users') && ! $this->hasOtherUserManager($user)) {
            throw ValidationException::withMessages(['user' => 'The last user manager cannot have their 2FA reset.']);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $this->activities->log('reset_user_2fa', 'Reset user 2FA', User::class, $user->id, [
            'email' => $user->email,
        ]);

        return back()->with('status', '2FA has been reset for this user.');
    }

    private function normalizeUserInput(Request $request): void
    {
        $normalized = [];

        foreach (['fname', 'mname', 'lname'] as $field) {
            if ($request->exists($field)) {
                $normalized[$field] = $request->filled($field) ? trim((string) $request->input($field)) : null;
            }
        }
        if ($request->exists('email')) {
            $normalized['email'] = strtolower(trim((string) $request->input('email')));
        }
        if ($request->exists('matric_no')) {
            $normalized['matric_no'] = $request->filled('matric_no') ? strtoupper(trim((string) $request->input('matric_no'))) : null;
        }
        if ($request->exists('staff_number')) {
            $normalized['staff_number'] = $request->filled('staff_number') ? strtoupper(trim((string) $request->input('staff_number'))) : null;
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
