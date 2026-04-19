<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    #Show Login Page
     public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }
    
    #Login process
    public function login(Request $request)
    {
        #input validation
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        #remember me functionality
        $remember = $request->has('remember');

        #login attempt
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')
            ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        #login failed
        return back()
            ->withInputs($request->only('email', 'remember'))
            ->withErrors(['email' => 'Invalid email or password.']);
    }

    #show register page
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.register');
    }

    #register process
        public function register(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            // Pesan error dalam Bahasa Indonesia
            'name.required'      => 'Name is required',
            'email.required'     => 'Email is required',
            'email.email'        => 'Invalid email format',
            'email.unique'       => 'This email is already registered',
            'password.required'  => 'Password is required',
            'password.confirmed' => 'Password confirmation does not match',
            'password.min'       => 'Password must be at least 8 characters',
        ]);

        #new user creation
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        #auto login after registration
        Auth::login($user);

            return redirect('/dashboard')
                ->with('success', 'Account created successfully! Welcome, ' . $user->name . '!');
        }
    
        #logout process
        public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'You have been logged out!');
    }
}


