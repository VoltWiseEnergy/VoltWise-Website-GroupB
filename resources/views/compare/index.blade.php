@extends('layouts.app')

@section('title', 'Compare Devices')

@section('styles')
<style>
.compare-form-card { max-width:680px; }
.compare-desc { font-size:0.8125rem; color:var(--text-muted); margin-bottom:1.25rem; line-height:1.5; }
.device-checklist { display:flex; flex-direction:column; gap:0.5rem; max-height:400px; overflow-y:auto; padding-right:0.5rem; }
.device-check-item {
    display:flex; align-items:center; gap:0.75rem;
    padding:0.75rem 1rem; border:1px solid var(--border); border-radius:10px;
    cursor:pointer; transition:border-color 0.15s, background 0.15s;
}
.device-check-item:hover { border-color:var(--blue-600); background:rgba(74,124,246,0.04); }
.device-check-item.selected { border-color:var(--blue-600); background:var(--blue-100); }
.device-check-item input[type="checkbox"] { width:18px; height:18px; accent-color:var(--blue-600); cursor:pointer; }
.device-check-name { font-weight:600; font-size:0.875rem; color:var(--text-primary); }
.device-check-meta { font-size:0.75rem; color:var(--text-muted); }
.compare-actions { display:flex; gap:0.75rem; margin-top:1.25rem; }
.compare-hint { font-size:0.75rem; color:var(--text-faint); margin-top:0.5rem; }
.btn-compare {
    display:inline-flex; align-items:center; gap:0.375rem;
    padding:0.6rem 1.25rem; background:var(--blue-600); color:#fff;
    border:none; border-radius:8px; font-size:0.875rem; font-weight:600;
    font-family:'Inter',sans-serif; cursor:pointer;
    transition:background 0.15s, transform 0.15s, box-shadow 0.15s;
    box-shadow:0 2px 8px rgba(74,124,246,0.3);
}
.btn-compare:hover { background:var(--blue-700); transform:translateY(-1px); box-shadow:0 4px 14px rgba(74,124,246,0.4); }
.btn-compare:disabled { opacity:0.5; cursor:not-allowed; transform:none; box-shadow:none; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">⚡ Device Comparison</h1>
        <p class="page-subtitle">Select devices to compare energy consumption, cost, and efficiency</p>
    </div>
</div>

@if($devices->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="empty-state" style="min-height:200px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
                <p>You haven't added any devices yet.<br>Add devices first to use the comparison tool.</p>
                <a href="{{ route('devices.create') }}" class="btn-primary" style="text-decoration:none; margin-top:0.5rem;">+ Add Device</a>
            </div>
        </div>
    </div>
@else
    <div class="card compare-form-card">
        <div class="card-body">
            <h2 class="card-title" style="margin-bottom:0.25rem;">Select Devices to Compare</h2>
            <p class="compare-desc">Choose between 2 and 4 devices for side-by-side comparison.</p>

            <form method="POST" action="{{ route('compare.results') }}" id="compareForm">
                @csrf

                <div class="device-checklist">
                    @foreach($devices as $device)
                    <label class="device-check-item" id="item-{{ $device->id }}">
                        <input type="checkbox" name="device_ids[]" value="{{ $device->id }}"
                               onchange="toggleItem(this, {{ $device->id }}); updateBtn();">
                        <div>
                            <div class="device-check-name">{{ $device->name }}</div>
                            <div class="device-check-meta">
                                {{ $device->wattage }}W · {{ $device->category }}
                                @if($device->brand) · {{ $device->brand }} @endif
                                @if($device->energy_label) · Label {{ strtoupper($device->energy_label) }} @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <p class="compare-hint" id="selectionHint">Select at least 2 devices</p>

                @if($errors->any())
                    <div style="color:#ef4444; font-size:0.8125rem; margin-top:0.5rem;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="compare-actions">
                    <button type="submit" class="btn-compare" id="compareBtn" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        Compare Devices
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
function toggleItem(checkbox, id) {
    const item = document.getElementById('item-' + id);
    if (checkbox.checked) {
        item.classList.add('selected');
    } else {
        item.classList.remove('selected');
    }
}

function updateBtn() {
    const checked = document.querySelectorAll('input[name="device_ids[]"]:checked').length;
    const btn = document.getElementById('compareBtn');
    const hint = document.getElementById('selectionHint');
    btn.disabled = checked < 2;
    if (checked === 0) hint.textContent = 'Select at least 2 devices';
    else if (checked === 1) hint.textContent = '1 selected — need at least 1 more';
    else if (checked > 4) { hint.textContent = 'Maximum 4 devices allowed'; btn.disabled = true; }
    else hint.textContent = checked + ' devices selected — ready to compare!';
}
</script>
@endsection
