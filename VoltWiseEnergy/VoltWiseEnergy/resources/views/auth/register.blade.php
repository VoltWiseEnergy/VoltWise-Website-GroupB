@extends('layouts.auth')

@section('title', 'Register')
@section('meta-desc', 'Create a new VoltWise Energy account')

{{-- ===================== LEFT PANEL CONTENT ===================== --}}
@section('panel-content')
    <h2>Join VoltWise<br>in 3 easy steps</h2>
    <p>Create your account and start monitoring your electricity usage today. It's free to get started.</p>

    <div class="steps-list">
        <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Create your account</strong>
                <span>Fill in your details below</span>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Add your devices</strong>
                <span>Register your electronic devices</span>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Track &amp; save energy</strong>
                <span>Get real-time insights and tips</span>
            </div>
        </div>
    </div>
@endsection

{{-- ===================== FORM CONTENT ===================== --}}
@section('form-content')
    {{-- Header --}}
    <div class="form-header">
        <h1>Create account</h1>
        <p>Start monitoring your energy for free</p>
    </div>

    {{-- Global error --}}
    @if ($errors->has('general'))
        <div class="alert alert-error" role="alert">
            <span class="alert-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <span>{{ $errors->first('general') }}</span>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
        @csrf

        {{-- ACCOUNT INFO --}}
        <div class="form-section-title">Account Info</div>

        {{-- Name --}}
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <input type="text" id="name" name="name"
                    class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    placeholder="John Doe" value="{{ old('name') }}"
                    autocomplete="name" required>
            </div>
            @error('name')
                <div class="field-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

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
                    placeholder="you@example.com" value="{{ old('email') }}"
                    autocomplete="email" required>
            </div>
            @error('email')
                <div class="field-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- SECURITY --}}
        <div class="form-section-title">Security</div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <input type="password" id="password" name="password"
                    class="form-input has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Min. 8 characters"
                    autocomplete="new-password" required
                    oninput="checkStrength(this.value)">
                <button type="button" class="toggle-password" onclick="togglePwd('password','eye-1')" aria-label="Show/hide password">
                    <svg id="eye-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <div class="strength-meter" id="strength-meter">
                <div class="strength-track">
                    <div class="strength-segment" id="seg1"></div>
                    <div class="strength-segment" id="seg2"></div>
                    <div class="strength-segment" id="seg3"></div>
                    <div class="strength-segment" id="seg4"></div>
                </div>
                <span class="strength-text" id="strength-text">Keep typing…</span>
            </div>
            @error('password')
                <div class="field-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </span>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="form-input has-toggle {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                    placeholder="Repeat your password"
                    autocomplete="new-password" required>
                <button type="button" class="toggle-password" onclick="togglePwd('password_confirmation','eye-2')" aria-label="Show/hide password">
                    <svg id="eye-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <div class="field-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Terms --}}
        <div class="form-group-terms">
            <label class="checkbox-label-terms">
                <input type="checkbox" name="terms" id="terms" required>
                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary" id="btn-register">
            <span class="btn-spinner" id="btn-spinner"></span>
            <span id="btn-text">Create Account</span>
        </button>
    </form>

    <div class="divider">or</div>
    <div class="card-footer">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
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

    function checkStrength(pass) {
        const meter = document.getElementById('strength-meter');
        const segs  = ['seg1','seg2','seg3','seg4'].map(id => document.getElementById(id));
        const label = document.getElementById('strength-text');
        segs.forEach(s => s.className = 'strength-segment');
        if (!pass) { meter.style.display = 'none'; return; }
        meter.style.display = 'block';
        let score = 0;
        if (pass.length >= 8)          score++;
        if (/[A-Z]/.test(pass))        score++;
        if (/[0-9]/.test(pass))        score++;
        if (/[^A-Za-z0-9]/.test(pass)) score++;
        const cfg = [
            { cls:'seg-weak',   text:'Weak',   color:'#ef4444' },
            { cls:'seg-fair',   text:'Fair',   color:'#f59e0b' },
            { cls:'seg-good',   text:'Good',   color:'#5b8af5' },
            { cls:'seg-strong', text:'Strong', color:'#10b981' },
        ];
        const lv = cfg[score - 1] || cfg[0];
        for (let i = 0; i < score; i++) segs[i].classList.add(lv.cls);
        label.textContent = lv.text;
        label.style.color = lv.color;
    }

    document.getElementById('register-form').addEventListener('submit', function(e) {
        if (!document.getElementById('terms').checked) {
            e.preventDefault();
            alert('Please agree to the Terms of Service to continue.');
            return;
        }
        document.getElementById('btn-text').textContent = 'Creating account…';
        document.getElementById('btn-spinner').style.display = 'block';
        document.getElementById('btn-register').disabled = true;
    });
@endsection
