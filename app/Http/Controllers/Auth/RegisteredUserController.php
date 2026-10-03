<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(protected ActivityService $activities) {}

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'matric_no' => strtoupper(trim((string) $request->input('matric_no'))),
        ]);

        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:100'],
            'mname' => ['nullable', 'string', 'max:100'],
            'lname' => ['required', 'string', 'max:100'],
            'matric_no' => 'required|string|max:50|unique:users,matric_no',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'fname' => trim($validated['fname']),
            'mname' => filled($validated['mname'] ?? null) ? trim($validated['mname']) : null,
            'lname' => trim($validated['lname']),
            'matric_no' => $validated['matric_no'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('student');

        event(new Registered($user));

        Auth::login($user);

        $this->activities->log('registered', 'Created account', User::class, $user->id, [
            'email' => $user->email,
            'matric_no' => $user->matric_no,
        ]);

        return redirect(route('dashboard', absolute: false));
    }
}
