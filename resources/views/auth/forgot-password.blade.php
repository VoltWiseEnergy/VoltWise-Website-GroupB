@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('meta-desc', 'Forgot your password?')

@section('panel-content')
    <h2>Oh no...</h2>
    <p>We are a high-tech energy platform, but we haven't figured out email servers yet.</p>
@endsection

@section('form-content')
    <div style="text-align: center; padding: 2rem;">
        <h1 style="margin-bottom: 1rem; color: var(--text-primary);">Forgot Password?</h1>
        
        <div style="margin-bottom: 2rem;">
            <!-- Make sure to place an image named 'forgot-password.png' inside your public/images folder! -->
            <img src="{{ asset('picture.png') }}" alt="Sorry please just make a new account meme" style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        </div>
        
        <p style="font-size: 1.2rem; margin-bottom: 2rem; color: var(--text-secondary);">
            Sorry, please just make a new account.
        </p>

        <a href="{{ route('register') }}" class="btn-primary" style="text-decoration: none; display: inline-block;">
            Create New Account
        </a>
        
        <div style="margin-top: 2rem;">
            <a href="{{ route('login') }}" style="color: var(--blue-600); text-decoration: none; font-weight: 600;">
                Never mind, I remembered it!
            </a>
        </div>
    </div>
@endsection
