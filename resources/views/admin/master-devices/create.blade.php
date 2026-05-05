@extends('layouts.app')
@section('title', 'Add Master Device')

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
            background: var(--icon-blue-bg); color: var(--icon-blue-fg);
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
        .fg .hint { font-size: 0.6875rem; color: var(--text-faint); margin-top: 0.25rem; }

        .fi, .fs, .ft {
            width: 100%; padding: 0.625rem 0.875rem;
            background: var(--bg-tip); border: 1.5px solid var(--border);
            border-radius: 8px; font-size: 0.875rem; font-family: 'Inter', sans-serif;
            color: var(--text-primary); outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .fi::placeholder, .ft::placeholder { color: var(--text-faint); }
        .fi:hover, .fs:hover, .ft:hover { border-color: var(--blue-200); }
        .fi:focus, .fs:focus, .ft:focus {
            border-color: var(--blue-600);
            box-shadow: 0 0 0 3px rgba(74,124,246,0.12);
            background: var(--bg-card);
        }
        .ft { min-height: 80px; resize: vertical; }
        .fe { font-size: 0.75rem; color: #ef4444; margin-top: 0.375rem; display: flex; align-items: center; gap: 0.25rem; }

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
            <h1 class="page-title">Add Master Device</h1>
            <p class="page-subtitle">Create a new device template for the library</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.master-devices.store') }}">
        @csrf
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </div>
                <div>
                    <h2>Device Information</h2>
                    <p>Fill in the details for the new device template</p>
                </div>
            </div>

            <div class="form-card-body">
                <div class="fg">
                    <label for="name">Device Name <span>*</span></label>
                    <input type="text" name="name" id="name" class="fi"
                           value="{{ old('name') }}" required placeholder="e.g. LED Bulb 10W">
                    <div class="hint">The name users will see when selecting a device</div>
                    @error('name') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>

                <div class="fg">
                    <label for="category">Category <span>*</span></label>
                    <select name="category" id="category" class="fs" required>
                        <option value="">Select a category...</option>
                        <option value="Lighting" {{ old('category') == 'Lighting' ? 'selected' : '' }}> Lighting</option>
                        <option value="Cooling" {{ old('category') == 'Cooling' ? 'selected' : '' }}> Cooling</option>
                        <option value="Heating" {{ old('category') == 'Heating' ? 'selected' : '' }}> Heating</option>
                        <option value="Kitchen" {{ old('category') == 'Kitchen' ? 'selected' : '' }}> Kitchen</option>
                        <option value="Entertainment" {{ old('category') == 'Entertainment' ? 'selected' : '' }}> Entertainment</option>
                        <option value="Laundry" {{ old('category') == 'Laundry' ? 'selected' : '' }}> Laundry</option>
                        <option value="Office" {{ old('category') == 'Office' ? 'selected' : '' }}> Office</option>
                        <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}> Other</option>
                    </select>
                    @error('category') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>

                <div class="fg">
                    <label for="wattage">Wattage <span>*</span></label>
                    <input type="number" name="wattage" id="wattage" class="fi"
                           value="{{ old('wattage') }}" required min="0" step="0.01"
                           placeholder="e.g. 100">
                    <div class="hint">Power consumption in watts (W)</div>
                    @error('wattage') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>

                <div class="fg">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="ft"
                              placeholder="Optional — describe what this device is or how it's used...">{{ old('description') }}</textarea>
                    @error('description') <div class="fe">⚠ {{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-card-footer">
                <a href="{{ route('admin.master-devices.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Device
                </button>
            </div>
        </div>
    </form>
@endsection
