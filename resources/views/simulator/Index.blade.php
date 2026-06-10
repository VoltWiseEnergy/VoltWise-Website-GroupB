@extends('layouts.app')
 
@section('title', 'Energy Cost Simulator')
 
@section('content')
 
<style>
    .sim-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; }
    .sim-header h2 { font-size:1.25rem; font-weight:600; color:var(--text-primary); margin-bottom:0.25rem; }
    .sim-header p  { font-size:0.8rem; color:var(--text-muted); margin:0; }
 
    .sim-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.5rem; }
    @media(max-width:640px){ .sim-grid { grid-template-columns:1fr; } }
 
    .card-vw {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; box-shadow:var(--shadow-card); padding:1.25rem;
    }
    .card-title { font-size:0.9rem; font-weight:600; color:var(--text-primary); margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem; }
    .card-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .dot-blue   { background:var(--blue-600); }
    .dot-green  { background:#059669; }
 
    .form-group { margin-bottom:0.85rem; }
    .form-label { font-size:0.78rem; color:var(--text-muted); margin-bottom:0.35rem; display:block; }
    .input-vw {
        width:100%; background:var(--bg-base); color:var(--text-primary);
        border:1px solid var(--border); border-radius:7px;
        padding:0.45rem 0.75rem; font-size:0.82rem; outline:none;
        transition:border-color 0.2s;
    }
    .input-vw:focus { border-color:var(--blue-600); }
    select.input-vw { cursor:pointer; }
 
    .btn-primary-vw {
        width:100%; background:var(--blue-600); color:#fff;
        border:none; border-radius:8px; padding:0.55rem 1rem;
        font-size:0.82rem; font-weight:500; cursor:pointer; margin-top:0.5rem;
    }
    .btn-primary-vw:hover { opacity:0.9; }
 
    .avg-hint { font-size:0.72rem; color:var(--text-muted); margin-top:0.25rem; }
    .avg-hint span { color:var(--blue-600); font-weight:500; }
 
    /* Scenario list */
    .scenario-list { display:flex; flex-direction:column; gap:0.75rem; }
    .scenario-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:10px; padding:0.9rem 1.1rem;
        display:flex; align-items:center; gap:1rem;
    }
    .scenario-info { flex:1; }
    .scenario-name { font-size:0.85rem; font-weight:600; color:var(--text-primary); margin-bottom:0.2rem; }
    .scenario-meta { font-size:0.75rem; color:var(--text-muted); }
    .saving-badge { font-size:0.72rem; border-radius:20px; padding:3px 10px; font-weight:500; }
    .saving-pos { background:#D1FAE5; color:#065F46; }
    .saving-neg { background:#FEE2E2; color:#991B1B; }
 
    .btn-view {
        background:var(--blue-600); color:#fff; border:none;
        border-radius:7px; padding:0.35rem 0.85rem;
        font-size:0.75rem; font-weight:500; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .btn-delete {
        background:transparent; color:var(--text-muted);
        border:1px solid var(--border); border-radius:7px;
        padding:0.35rem 0.75rem; font-size:0.75rem; cursor:pointer;
    }
    .btn-delete:hover { border-color:#EF4444; color:#EF4444; }
 
    .empty-state { text-align:center; padding:2.5rem 1rem; }
    .empty-state .icon { font-size:2rem; margin-bottom:0.5rem; }
    .empty-state p { font-size:0.8rem; color:var(--text-muted); }
</style>
 
<div style="padding:1.5rem;">
 
    {{-- Header --}}
    <div class="sim-header">
        <div>
            <h2>Energy Cost Simulator</h2>
            <p>Simulate usage changes and view estimated electricity savings</p>
        </div>
    </div>
 
    @if(session('success'))
        <div style="background:#D1FAE5; color:#065F46; border-radius:8px; padding:0.65rem 1rem; font-size:0.8rem; margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif
 
    <div class="sim-grid">
 
        {{-- PBI #56 — Form input skenario --}}
        <div class="card-vw">
            <div class="card-title">
                <span class="card-dot dot-blue"></span>
                Create New Scenario
            </div>
 
            @if($devices->isEmpty())
                <div class="empty-state">
                    <div class="icon">📱</div>
                    <p>Add a device first to create a scenario.</p>
                    <a href="{{ route('devices.create') }}" style="color:var(--blue-600); font-size:0.8rem;">+ Add Device</a>
                </div>
            @else
                <form action="{{ route('simulator.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Scenario Name</label>
                        <input type="text" name="name" class="input-vw"
                               placeholder="ex: Reduce AC 50%" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Choose Device</label>
                        <select name="device_id" class="input-vw" id="device-select" required
                                onchange="updateHint(this)">
                            <option value="">-- Choose device --</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}"
                                        data-avg="{{ $avgUsage[$device->id] ?? 0 }}"
                                        data-watt="{{ $device->wattage }}">
                                    {{ $device->name }} ({{ $device->wattage }}W)
                                </option>
                            @endforeach
                        </select>
                        <div class="avg-hint" id="avg-hint" style="display:none;">
                            Average actual usage 7 days: <span id="avg-val">0</span> hour/day
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hours / Day</label>
                        <input type="number" name="scenario_hours" class="input-vw"
                               min="0" max="24" step="0.5"
                               placeholder="ex: 4" required>
                        <div class="avg-hint">Enter the hypothetical hours you want to simulate</div>
                    </div>
                    <button type="submit" class="btn-primary-vw">Calculate Simulation</button>
                </form>
            @endif
        </div>
 
        {{-- Info tariff --}}
        <div class="card-vw" style="display:flex; flex-direction:column; gap:0.75rem;">
            <div class="card-title">
                <span class="card-dot dot-green"></span>
                Tariff Information
            </div>
            <div style="font-size:0.8rem; color:var(--text-muted); line-height:1.7;">
                Simulation uses the default PLN electricity tariff.
            </div>
            <div style="background:var(--bg-base); border-radius:8px; padding:0.85rem; text-align:center;">
                <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.25rem;">Current Tariff</div>
                <div style="font-size:1.4rem; font-weight:600; color:var(--blue-600);">Rp 1.444</div>
                <div style="font-size:0.72rem; color:var(--text-muted);">per kWh</div>
            </div>
            <div style="font-size:0.75rem; color:var(--text-muted); line-height:1.6; border-left:2px solid var(--border); padding-left:0.75rem;">
                Tariff will be updated automatically after admin sets the tariff in the system.
            </div>
        </div>
    </div>
 
    {{-- Daftar skenario tersimpan --}}
    <div class="card-vw">
        <div class="card-title" style="margin-bottom:1rem;">
            <span class="card-dot" style="background:#8B5CF6;"></span>
            Saved Scenarios
        </div>
 
        @if($scenarios->isEmpty())
            <div class="empty-state" style="padding:1.5rem;">
                <div class="icon">📊</div>
                <p>No scenarios found. Create your first scenario above.</p>
            </div>
        @else
            <div class="scenario-list">
                @foreach($scenarios as $scenario)
                <div class="scenario-card">
                    <div class="scenario-info">
                        <div class="scenario-name">{{ $scenario->name }}</div>
                        <div class="scenario-meta">
                            {{ $scenario->device_name }} · {{ $scenario->wattage }}W ·
                            {{ $scenario->current_hours }} → {{ $scenario->scenario_hours }} hours/day
                        </div>
                    </div>
                    @php $saving = $scenario->saving_percent; @endphp
                    <span class="saving-badge {{ $saving >= 0 ? 'saving-pos' : 'saving-neg' }}">
                        {{ $saving >= 0 ? '-' : '+' }}{{ abs($saving) }}%
                    </span>
                    <a href="{{ route('simulator.show', $scenario) }}" class="btn-view">View</a>
                    <form action="{{ route('simulator.destroy', $scenario) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete"
                                onclick="return confirm('Delete this scenario?')">Delete</button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>
 
</div>
 
<script>
function updateHint(select) {
    const opt  = select.options[select.selectedIndex];
    const avg  = opt.dataset.avg;
    const hint = document.getElementById('avg-hint');
    const val  = document.getElementById('avg-val');
    if (select.value) {
        hint.style.display = 'block';
        val.textContent = avg || '0';
    } else {
        hint.style.display = 'none';
    }
}
</script>
 
@endsection