@extends('layouts.app')
@section('title', 'Add Master Device')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Add Master Device</h1>
            <p class="page-subtitle">Create a new device template for the library</p>
        </div>
        <a href="{{ route('admin.master-devices.index') }}" class="btn-secondary">← Back to List</a>
    </div>

    <div class="card" style="max-width:600px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.master-devices.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="name">Device Name *</label>
                    <input type="text" name="name" id="name" class="form-input"
                           value="{{ old('name') }}" required placeholder="e.g. LED Bulb 10W">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="category">Category *</label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="">Select category...</option>
                        <option value="Lighting" {{ old('category') == 'Lighting' ? 'selected' : '' }}>
                            Lighting
                        </option>
                        <option value="Cooling" {{ old('category') == 'Cooling' ? 'selected' : '' }}>
                            Cooling
                        </option>
                        <option value="Heating" {{ old('category') == 'Heating' ? 'selected' : '' }}>
                            Heating
                        </option>
                        <option value="Kitchen" {{ old('category') == 'Kitchen' ? 'selected' : '' }}>
                            Kitchen
                        </option>
                        <option value="Entertainment" {{ old('category') == 'Entertainment' ? 'selected' : '' }}>
                            Entertainment
                        </option>
                        <option value="Laundry" {{ old('category') == 'Laundry' ? 'selected' : '' }}>
                            Laundry
                        </option>
                        <option value="Office" {{ old('category') == 'Office' ? 'selected' : '' }}>
                            Office
                        </option>
                        <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>
                            Other
                        </option>
                    </select>
                    @error('category')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="wattage">Wattage (W) *</label>
                    <input type="number" name="wattage" id="wattage" class="form-input"
                           value="{{ old('wattage') }}" required min="0" step="0.01"
                           placeholder="e.g. 100">
                    @error('wattage')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" class="form-textarea"
                              placeholder="Optional description...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-primary" style="margin-top:0.5rem;">
                    Add Master Device
                </button>
            </form>
        </div>
    </div>
@endsection
