@extends('layouts.app')
@section('title', 'Edit Master Device')

@section('content')
    <style>
        .form-card {
            max-width: 640px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }
        .form-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 0.75rem;
        }
        .form-card-header-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: var(--icon-orange-bg); color: var(--icon-orange-fg);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .form-card-header-icon svg { width: 18px; height: 18px; }
        .form-card-header h2 { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
        .form-card-header p { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
        .form-card-body { padding: 1.5rem; }

        .fg { margin-bottom: 1.25rem; }
        .fg:last-of-type { margin-bottom: 0; }
        .fg label {
            display: block; font-size: 0.8125rem; font-weight: 600;
            color: var(--text-secondary); margin-bottom: 0.375rem;
        }
        .fg label span { color: #ef4444; }

        .fi, .fs, .ft {
            width: 100%; padding: 0.625rem 0.875rem;
            background: var(--bg-tip); border: 1.5px solid var(--border);
            border-radius: 8px; font-size: 0.875rem; font-family: 'Inter', sans-serif;
            color: var(--text-primary); outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .fi:focus, .fs:focus, .ft:focus {
            border-color: var(--blue-600);
            box-shadow: 0 0 0 3px rgba(74,124,246,0.12);
            background: var(--bg-card);
        }
        .ft { min-height: 80px; resize: vertical; }
        .fe { font-size: 0.75rem; color: #ef4444; margin-top: 0.375rem; }

        .form-card-footer {
            padding: 1rem 1.5rem; border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: flex-end; gap: 0.625rem;
            background: var(--bg-tip);
        }
        .btn-cancel {
            padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500;
            color: var(--text-muted); background: var(--bg-card); border: 1px solid var(--border);
            text-decoration: none; font-family: 'Inter', sans-serif; cursor: pointer;
            transition: all 0.15s;
        }
        .btn-cancel:hover { border-color: var(--text-faint); color: var(--text-primary); }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Master Device</h1>
            <p class="page-subtitle">Update "{{ $masterDevice->name }}"</p>
        </div>
    </div>

        {{-- Device Info Banner --}}    
    <div style="max-width: 640px; display: flex; align-items: center; gap: 1rem;
                padding: 0.75rem 1rem; margin-bottom: 1rem;
                background: var(--bg-banner); border: 1px solid var(--bg-banner-border);
                border-radius: 10px;">
        <div style="width: 36px; height: 36px; border-radius: 8px;
                    background: var(--icon-orange-bg); color: var(--icon-orange-fg);
                    display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
        </div>
        <div style="flex: 1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-primary);">
                    {{ $masterDevice->name }}
                    <span style="display: inline-block; padding: 0.125rem 0.5rem; margin-left: 0.375rem;
                                 background: var(--icon-blue-bg); color: var(--icon-blue-fg);
                                 border-radius: 5px; font-size: 0.6875rem; font-weight: 600;">
                        {{ $masterDevice->category }}
                    </span>
                </div>
                <div style="font-size: 0.6875rem; color: var(--text-muted); margin-top: 2px;">
                    Created {{ $masterDevice->created_at->format('d M Y') }}
                    · Last updated {{ $masterDevice->updated_at->diffForHumans() }}
                </div>
            </div>
            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--text-primary);">
                {{ number_format($masterDevice->wattage, 0) }} W
            </div>
        </div>
    </div>


    <form method="POST" action="{{ route('admin.master-devices.update', $masterDevice) }}">
        @csrf
        @method('PUT')
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div>
                    <h2>Edit Device</h2>
                    <p>Update the details for "{{ $masterDevice->name }}"</p>
                </div>
            </div>

            <div class="form-card-body">
                <div class="fg">
                    <label for="name">Device Name <span>*</span></label>
                    <input type="text" name="name" id="name" class="fi"
                           value="{{ old('name', $masterDevice->name) }}" required>
                    @error('name') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>

                <div class="fg">
                    <label for="category">Category <span>*</span></label>
                    <select name="category" id="category" class="fs" required>
                        @foreach(['Lighting','Cooling','Heating','Kitchen','Entertainment','Laundry','Office','Other'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $masterDevice->category) == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('category') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>

                <div class="fg">
                    <label for="wattage">Wattage (W) <span>*</span></label>
                    <input type="number" name="wattage" id="wattage" class="fi"
                           value="{{ old('wattage', $masterDevice->wattage) }}" required min="0" step="0.01">
                    @error('wattage') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>

                <div class="fg">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="ft">{{ old('description', $masterDevice->description) }}</textarea>
                    @error('description') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-card-footer">
                <a href="{{ route('admin.master-devices.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </form>
@endsection
