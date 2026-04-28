@extends('layouts.app')
@section('title', 'Master Devices')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Master Device Library</h1>
            <p class="page-subtitle">Manage device templates available to all users</p>
        </div>
        <a href="{{ route('admin.master-devices.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Device
        </a>
    </div>

    {{-- Stats Summary --}}
    <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.5rem;">
        <div class="card">
            <div class="card-body">
                <div class="stat-header">
                    <span class="stat-label">Total Devices</span>
                    <div class="stat-icon icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $totalDevices }}</div>
                <div class="stat-detail">Device templates in library</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="stat-header">
                    <span class="stat-label">Categories</span>
                    <div class="stat-icon icon-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $totalCategories }}</div>
                <div class="stat-detail">Unique device categories</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="stat-header">
                    <span class="stat-label">Avg. Wattage</span>
                    <div class="stat-icon icon-orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $avgWattage }} <small>W</small></div>
                <div class="stat-detail">Average power consumption</div>
            </div>
        </div>
    </div>

    {{-- Device Table --}}
    @if($masterDevices->count() > 0)
        <div class="card">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
                        display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div class="card-title">All Devices</div>
                    <div class="card-subtitle">{{ $totalDevices }} device{{ $totalDevices !== 1 ? 's' : '' }} in library</div>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem;
                                       font-weight: 600; color: var(--text-muted); text-transform: uppercase;
                                       letter-spacing: 0.05em;">#</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem;
                                       font-weight: 600; color: var(--text-muted); text-transform: uppercase;
                                       letter-spacing: 0.05em;">Name</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem;
                                       font-weight: 600; color: var(--text-muted); text-transform: uppercase;
                                       letter-spacing: 0.05em;">Category</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem;
                                       font-weight: 600; color: var(--text-muted); text-transform: uppercase;
                                       letter-spacing: 0.05em;">Wattage</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem;
                                       font-weight: 600; color: var(--text-muted); text-transform: uppercase;
                                       letter-spacing: 0.05em;">Description</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: right; font-size: 0.75rem;
                                       font-weight: 600; color: var(--text-muted); text-transform: uppercase;
                                       letter-spacing: 0.05em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masterDevices as $i => $device)
                            <tr style="border-bottom: 1px solid var(--border); transition: background 0.1s;"
                                onmouseover="this.style.background='var(--bg-tip)'"
                                onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.875rem 1.25rem; font-size: 0.8125rem; color: var(--text-faint);">
                                    {{ $i + 1 }}
                                </td>
                                <td style="padding: 0.875rem 1.25rem;">
                                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                                        <div style="width: 32px; height: 32px; border-radius: 8px;
                                                    background: var(--icon-blue-bg); color: var(--icon-blue-fg);
                                                    display: flex; align-items: center; justify-content: center;
                                                    flex-shrink: 0;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.8125rem; font-weight: 600;
                                                        color: var(--text-primary);">
                                                {{ $device->name }}
                                            </div>
                                            <div style="font-size: 0.6875rem; color: var(--text-faint);">
                                                Added {{ $device->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.875rem 1.25rem;">
                                    <span style="display: inline-block; padding: 0.25rem 0.625rem;
                                                 background: var(--icon-blue-bg); color: var(--icon-blue-fg);
                                                 border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                        {{ $device->category }}
                                    </span>
                                </td>
                                <td style="padding: 0.875rem 1.25rem; font-size: 0.8125rem;
                                           font-weight: 600; color: var(--text-primary);">
                                    {{ number_format($device->wattage, 0) }} W
                                </td>
                                <td style="padding: 0.875rem 1.25rem; font-size: 0.8125rem;
                                           color: var(--text-muted); max-width: 200px;
                                           overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $device->description ?? '—' }}
                                </td>
                                <td style="padding: 0.875rem 1.25rem; text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.375rem;">
                                        <a href="{{ route('admin.master-devices.edit', $device) }}"
                                           style="display: inline-flex; align-items: center; gap: 0.25rem;
                                                  padding: 0.375rem 0.75rem; border-radius: 6px;
                                                  background: var(--icon-btn-bg); border: 1px solid var(--border);
                                                  color: var(--text-muted); font-size: 0.75rem; font-weight: 500;
                                                  text-decoration: none; transition: all 0.15s;"
                                           onmouseover="this.style.borderColor='var(--blue-600)';this.style.color='var(--blue-600)'"
                                           onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST"
                                              action="{{ route('admin.master-devices.destroy', $device) }}"
                                              onsubmit="return confirm('Delete {{ $device->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    style="display: inline-flex; align-items: center; gap: 0.25rem;
                                                           padding: 0.375rem 0.75rem; border-radius: 6px;
                                                           background: var(--icon-btn-bg); border: 1px solid var(--border);
                                                           color: var(--text-muted); font-size: 0.75rem; font-weight: 500;
                                                           font-family: 'Inter', sans-serif; cursor: pointer;
                                                           transition: all 0.15s;"
                                                    onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444';this.style.background='#fef2f2'"
                                                    onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)';this.style.background='var(--icon-btn-bg)'">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                     stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Better Empty State --}}
        <div class="card">
            <div style="padding: 3rem 2rem; text-align: center;">
                <div style="width: 64px; height: 64px; border-radius: 16px;
                            background: var(--icon-blue-bg); color: var(--icon-blue-fg);
                            display: flex; align-items: center; justify-content: center;
                            margin: 0 auto 1.25rem;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary);
                           margin-bottom: 0.5rem;">No devices in library</h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); max-width: 360px;
                          margin: 0 auto 1.5rem; line-height: 1.5;">
                    Your master device library is empty. Add your first device template so users can select from a predefined list.
                </p>
                <a href="{{ route('admin.master-devices.create') }}" class="btn-primary"
                   style="display: inline-flex; margin: 0 auto;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add First Device
                </a>
            </div>
        </div>
    @endif
@endsection
