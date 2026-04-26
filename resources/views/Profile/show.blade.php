@extends('layouts.app')
@section('title', 'Profile')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Your personal account information</p>
    </div>
</div>

<div class="profile-grid">

    {{-- Left: Avatar Card --}}
    <div class="card profile-avatar-card">
        <div class="card-body" style="display:flex; flex-direction:column; align-items:center; text-align:center; gap:1rem;">
            <div class="profile-avatar-wrap">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" class="profile-avatar-img" alt="{{ $user->name }}">
                @else
                    <div class="profile-avatar-placeholder">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div>
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-email-sub">{{ $user->email }}</div>
            </div>

            <div class="profile-member-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                VoltWise Member
            </div>
        </div>
    </div>

    {{-- Right: Info Card --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title" style="margin-bottom:1.25rem;">Account Information</div>

            <div class="profile-info-list">
                <div class="profile-info-row">
                    <div class="profile-info-icon icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="profile-info-content">
                        <div class="profile-info-label">Full Name</div>
                        <div class="profile-info-value">{{ $user->name }}</div>
                    </div>
                </div>

                <div class="profile-info-divider"></div>

                <div class="profile-info-row">
                    <div class="profile-info-icon icon-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="profile-info-content">
                        <div class="profile-info-label">Email Address</div>
                        <div class="profile-info-value">{{ $user->email }}</div>
                    </div>
                </div>

                <div class="profile-info-divider"></div>

                <div class="profile-info-row">
                    <div class="profile-info-icon icon-orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.29 6.29l.61-.61a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="profile-info-content">
                        <div class="profile-info-label">Phone Number</div>
                        <div class="profile-info-value {{ !$user->phone ? 'profile-info-empty' : '' }}">
                            {{ $user->phone ?? 'Not set yet' }}
                        </div>
                    </div>
                </div>

                <div class="profile-info-divider"></div>

                <div class="profile-info-row">
                    <div class="profile-info-icon icon-purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8"  y1="2" x2="8"  y2="6"/>
                            <line x1="3"  y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="profile-info-content">
                        <div class="profile-info-label">Member Since</div>
                        <div class="profile-info-value">{{ $user->created_at->format('d F Y') }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@section('styles')
<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 1rem;
        align-items: start;
    }
    .profile-avatar-card .card-body { padding: 2rem 1.25rem; }
    .profile-avatar-wrap { position: relative; display: inline-block; }
    .profile-avatar-img {
        width: 96px; height: 96px; border-radius: 50%;
        object-fit: cover; border: 3px solid var(--border);
    }
    .profile-avatar-placeholder {
        width: 96px; height: 96px; border-radius: 50%;
        background: var(--blue-100); border: 3px solid var(--blue-200);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 700; color: var(--blue-600);
    }
    .profile-name { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; }
    .profile-email-sub { font-size: 0.75rem; color: var(--text-muted); }
    .profile-member-badge {
        display: inline-flex; align-items: center; gap: 0.375rem;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        background: var(--icon-blue-bg); color: var(--icon-blue-fg);
        font-size: 0.75rem; font-weight: 600; border: 1px solid var(--blue-200);
    }
    .profile-info-list { display: flex; flex-direction: column; }
    .profile-info-row { display: flex; align-items: center; gap: 1rem; padding: 0.875rem 0; }
    .profile-info-icon {
        width: 36px; height: 36px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .profile-info-icon svg { width: 16px; height: 16px; }
    .profile-info-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.2rem; }
    .profile-info-value { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
    .profile-info-empty { color: var(--text-faint); font-style: italic; font-weight: 400; }
    .profile-info-divider { height: 1px; background: var(--border); }

    @media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }
</style>
@endsection