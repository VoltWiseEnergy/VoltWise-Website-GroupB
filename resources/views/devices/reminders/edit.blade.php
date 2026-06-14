@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Reminder</h1>
        <p class="page-subtitle">
            Update the reminder time or message for <strong>{{ $device->name }}</strong>.
        </p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="error-box">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('devices.reminders.update', $device->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Device</label>
                <input type="text" class="form-input" value="{{ $device->name }}" disabled>
            </div>

            <div class="form-group">
                <label class="form-label">Scheduled Time</label>
                <input type="time" name="reminder_time" class="form-input" value="{{ old('reminder_time', $device->reminder_time) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Reminder Message</label>
                <input type="text" name="reminder_message" class="form-input" placeholder="e.g. Turn off AC at 10PM" value="{{ old('reminder_message', $device->reminder_message) }}">
            </div>

            <div class="form-actions">
                <a href="{{ route('devices.index') }}" class="btn-cancel">Back</a>
                <button name="save-reminder-edit" type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>

        <form action="{{ route('devices.reminders.destroy', $device->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete" onclick="return confirm('Delete this reminder?')">Delete Reminder</button>
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

.form-input[type="time"] {
    cursor: pointer;
}

.form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 25px;
}

.btn-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border: 1px solid var(--border);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-muted);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}

.btn-cancel:hover {
    background: var(--nav-hover-bg);
    color: var(--text-primary);
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    background: var(--blue-600);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.15s, transform 0.15s;
}

.btn-primary:hover {
    background: var(--blue-700);
    transform: translateY(-1px);
}

.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ef4444;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: opacity 0.15s;
    margin-top: 18px;
}

.btn-delete:hover {
    opacity: 0.9;
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeInput = document.querySelector('input[type="time"][name="reminder_time"]');
        if (timeInput && typeof timeInput.showPicker === 'function') {
            timeInput.addEventListener('click', function () {
                this.showPicker();
            });
            timeInput.addEventListener('focus', function () {
                this.showPicker();
            });
        }
    });
</script>
@endsection
