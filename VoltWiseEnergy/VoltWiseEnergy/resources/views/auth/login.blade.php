@extends('layouts.auth')

@section('title', 'Login')
@section('meta-desc', 'Login to your VoltWise Energy account')

{{-- ===================== LEFT PANEL CONTENT ===================== --}}
@section('panel-content')
    <h2>Monitor your energy,<br>save more money</h2>
    <p>VoltWise helps you track electricity consumption across all your devices and discover opportunities to reduce your bills.</p>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-item-icon">📊</div>
            <span>Real-time consumption tracking</span>
        </div>
        <div class="feature-item">
            <div class="feature-item-icon">💡</div>
            <span>Personalized energy-saving tips</span>
        </div>
        <div class="feature-item">
            <div class="feature-item-icon">🌱</div>
            <span>Support SDG 7 sustainable goals</span>
        </div>
    </div>
@endsection

{{-- ===================== FORM CONTENT ===================== --}}
@section('form-content')
    {{-- Header --}}
    <div class="form-header">
        <h1>Welcome back</h1>
        <p>Sign in to your VoltWise account</p>
    </div>

    {{-- Flash success (after logout) --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            <span class="alert-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Error (wrong credentials) --}}
    @if ($errors->has('email'))
        <div class="alert alert-error" role="alert">
            <span class="alert-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <span>{{ $errors->first('email') }}</span>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
                <input type="email" id="email" name="email"
                    class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    autocomplete="email" required>
            </div>
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <input type="password" id="password" name="password"
                    class="form-input has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Enter your password"
                    autocomplete="current-password" required>
                <button type="button" class="toggle-password" onclick="togglePwd('password','eye-login')" aria-label="Show/hide password">
                    <svg id="eye-login" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember + Forgot --}}
        <div class="form-row">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
            <a href="#" class="form-link">Forgot password?</a>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary" id="btn-login">
            <span class="btn-spinner" id="btn-spinner"></span>
            <span id="btn-text">Sign In</span>
        </button>
    </form>

    <div class="divider">or</div>
    <div class="card-footer">
        Don't have an account? <a href="{{ route('register') }}">Create one now</a>
    </div>
@endsection

{{-- ===================== PAGE SCRIPTS ===================== --}}
@section('page-scripts')
    function togglePwd(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.innerHTML = show
            ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    }

    document.getElementById('login-form').addEventListener('submit', function () {
        document.getElementById('btn-text').textContent = 'Signing in…';
        document.getElementById('btn-spinner').style.display = 'block';
        document.getElementById('btn-login').disabled = true;
    });
@endsection
