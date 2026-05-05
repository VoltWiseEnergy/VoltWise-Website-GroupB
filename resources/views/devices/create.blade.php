@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Add New Device</h1>
        <p class="page-subtitle">
            Register a new electrical device to monitor its energy usage.
        </p>
    </div>
</div>

@if($errors->any())
    <div class="error-box">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">

        <form action="{{ route('devices.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Device Name</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-input"
                    placeholder="e.g. Air Conditioner"
                    value="{{ old('name') }}"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Wattage</label>
                <input 
                    type="number" 
                    name="wattage" 
                    class="form-input"
                    placeholder="e.g. 900"
                    value="{{ old('wattage') }}"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category" class="form-input">
                    <option value="">Select Category</option>
                    <option value="Kitchen" {{ old('category') == 'Kitchen' ? 'selected' : '' }}>Kitchen</option>
                    <option value="Entertainment" {{ old('category') == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                    <option value="Lighting" {{ old('category') == 'Lighting' ? 'selected' : '' }}>Lighting</option>
                    <option value="Cooling" {{ old('category') == 'Cooling' ? 'selected' : '' }}>Cooling</option>
                    <option value="Cleaning" {{ old('category') == 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                    <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="form-actions">
                <a href="{{ route('devices.index') }}" class="btn-cancel">
                    Cancel
                </a>

                <button type="submit" class="btn-primary">
                    Add Device
                </button>
            </div>
        </form>

    </div>
</div>
@endsection


@section('styles')
<style>
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--bg-card);
    color: var(--text-primary);
    font-size: 14px;
}

.form-input:focus {
    outline: none;
    border-color: var(--blue-600);
    box-shadow: 0 0 0 3px rgba(74,124,246,0.15);
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.btn-cancel {
    padding: 10px 18px;
    border: 1px solid var(--border);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-muted);
    font-weight: 600;
}

.btn-cancel:hover {
    background: var(--nav-hover-bg);
}

.error-box {
    background: #fee2e2;
    color: #b91c1c;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.error-box ul {
    margin: 0;
    padding-left: 20px;
}
</style>
@endsection