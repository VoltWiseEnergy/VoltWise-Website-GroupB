<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->isAdmin() ? '/admin/dashboard' : '/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Input validation
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Remember me functionality
        $remember = $request->boolean('remember');

        // Login attempt
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $home = $user->isAdmin() ? '/admin/dashboard' : '/dashboard';

            return redirect()->intended($home)
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Login failed
        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Invalid email or password.']);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->isAdmin() ? '/admin/dashboard' : '/dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Input validation
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'      => 'Name is required.',
            'email.required'     => 'Email is required.',
            'email.email'        => 'Invalid email format.',
            'email.unique'       => 'This email is already registered.',
            'password.required'  => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min'       => 'Password must be at least 8 characters.',
        ]);

        // Create new user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Auto-login after registration
        Auth::login($user);

        return redirect('/dashboard')
            ->with('success', 'Account created successfully! Welcome, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'You have been logged out successfully.');
    }
}