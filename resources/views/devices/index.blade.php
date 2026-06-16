@extends('layouts.app')

@section('content')

<!-- Notification Container -->
<div id="notification-container" class="notification-container"></div>

<div class="page-header">
    <div>
        <h1 class="page-title">My Devices</h1>
        <p class="page-subtitle">
            Manage your registered electrical devices and track their energy usage.
        </p>
    </div>

    <a href="{{ route('devices.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Device
    </a>
</div>

@if(!empty($dueReminders) && $dueReminders->count() > 0)
    <div class="due-alert">
        <strong>Reminder Due Now:</strong>
        <div class="due-alert-list">
            @foreach($dueReminders as $reminderDevice)
                <span>{{ $reminderDevice->reminder_message ?? 'It\'s time to check this device.' }} for <strong>{{ $reminderDevice->name }}</strong>.</span>
            @endforeach
        </div>
    </div>
@endif

<div class="page-tabs">
    <button type="button" class="tab-button active" data-tab="device-list">Device List</button>
    <button name="manage-reminder-btn" type="button" class="tab-button" data-tab="device-reminder">Manage Reminders</button>
</div>

<div class="tab-panel active" id="device-list">
    @if(!empty($dueReminders) && $dueReminders->count() > 0)
        <div class="alert alert-success" style="margin-bottom:1.25rem; padding: 18px; border-radius: 14px; background: rgba(16,185,129,0.12); border: 1px solid #10b981; color: #065f46;">
            <strong>Reminder Alert:</strong>
            @foreach($dueReminders as $reminderDevice)
                <div style="margin-top: 0.5rem;">
                    {{ $reminderDevice->reminder_message ?? 'It\'s time to check this device.' }} for <strong>{{ $reminderDevice->name }}</strong>.
                </div>
            @endforeach
        </div>
    @endif

    <div class="card">
    <div class="card-body">

        @if($devices->count() > 0)
            <div class="device-table-wrapper">
                <table class="device-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Device Name</th>
                            <th>Wattage</th>
                            <th>Category</th>
                            <th>Reminder</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($devices as $device)
                            @php
                                $reminderIsOn = $device->reminder_enabled && $device->reminder_time;
                                $reminderDue = $reminderIsOn && \Carbon\Carbon::parse($device->reminder_time)->format('H:i') === now()->format('H:i');
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $device->name }}</td>
                                <td>{{ $device->wattage }} W</td>
                                <td>
                                    <span class="category-badge">
                                        {{ $device->category }}
                                    </span>
                                </td>
                                <td>
                                    @if($reminderIsOn)
                                        <div
                                            class="reminder-chip {{ $reminderDue ? 'reminder-due' : '' }}"
                                            data-reminder-time="{{ \Carbon\Carbon::parse($device->reminder_time)->format('H:i') }}"
                                            data-reminder-message="{{ e($device->reminder_message) }}"
                                            >
                                            <strong>{{ \Carbon\Carbon::parse($device->reminder_time)->format('g:i A') }}</strong>
                                            <div>{{ $device->reminder_message }}</div>
                                            @if($reminderDue)
                                                <span class="reminder-now">Due now</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="reminder-none">No reminder</span>
                                    @endif
                                </td>
                                <td class="action-buttons">
                                    <a href="{{ route('devices.edit', $device->id) }}" class="btn-edit" name="edit-reminder-btn">
                                        Edit
                                    </a>

                                    <form action="{{ route('devices.destroy', $device->id) }}" 
                                          method="POST"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn-delete"
                                                onclick="return confirm('Are you sure you want to delete this device?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                    <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"></path>
                </svg>

                <h3>No Devices Yet</h3>
                <p>Add your first device to start tracking energy usage.</p>
            </div>
        @endif

    </div>
</div>
</div>

<div class="tab-panel" id="device-reminder">
    <div class="card">
        <div class="card-body">
            <div class="reminder-header">
                <h2>Manage Device Reminders</h2>
                <p>View active reminders, schedule new ones, or update and remove existing reminders.</p>
            </div>

            <div class="reminder-grid">
                <div class="reminder-card">
                    <h3>Schedule New Reminder</h3>
                    @if($devices->count() > 0)
                        @if($errors->any())
                            <div class="error-box">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('devices.reminders.schedule') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label class="form-label">Device</label>
                                <select name="device_id" class="form-input" required>
                                    <option value="">Choose device</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                            {{ $device->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Scheduled Time</label>
                                <input type="time" name="reminder_time" class="form-input" value="{{ old('reminder_time') }}" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Reminder Message</label>
                                <input type="text" name="reminder_message" class="form-input" placeholder="e.g. Turn off AC at 10PM" value="{{ old('reminder_message') }}">
                            </div>

                            <div class="form-actions">
                                <button name="save-reminder-btn" type="submit" class="btn-primary">Save Reminder</button>
                            </div>
                        </form>
                    @else
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"></path>
                            </svg>

                            <h3>No Devices Available</h3>
                            <p>Add a device first, then schedule reminders here.</p>
                        </div>
                    @endif
                </div>

                <div class="reminder-card reminders-list-card">
                    <h3>Active Reminders</h3>
                    @if($reminderDevices->count() > 0)
                        <div class="reminder-table-wrapper">
                            <table class="reminder-table">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>Time</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reminderDevices as $reminderDevice)
                                        <tr>
                                            <td>{{ $reminderDevice->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($reminderDevice->reminder_time)->format('g:i A') }}</td>
                                            <td>{{ $reminderDevice->reminder_message }}</td>
                                            <td class="reminder-actions">
                                                <a href="{{ route('devices.reminders.edit', $reminderDevice->id) }}" class="btn-edit small">Edit</a>
                                                <form action="{{ route('devices.reminders.destroy', $reminderDevice->id) }}" method="POST" class="inline-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button name="delete-reminder" type="submit" class="btn-delete small" onclick="return confirm('Delete this reminder?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2"></rect>
                                <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"></path>
                            </svg>

                            <h3>No Reminders Yet</h3>
                            <p>Create a reminder using the form on the left.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.page-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.tab-button {
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-primary);
    padding: 12px 20px;
    border-radius: 999px;
    cursor: pointer;
    font-weight: 600;
}

.tab-button.active {
    background: var(--blue-600);
    border-color: transparent;
    color: white;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

.reminder-header {
    margin-bottom: 20px;
}

.reminder-header h2 {
    margin-bottom: 8px;
}

.due-alert {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 18px 22px;
    border: 1px solid #10b981;
    background: rgba(16,185,129,0.12);
    color: #065f46;
    border-radius: 16px;
    margin-bottom: 20px;
}

.due-alert-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.reminder-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 20px;
}

.reminder-card {
    padding: 24px;
    border-radius: 20px;
    background: var(--bg-card);
    border: 1px solid var(--border);
}

.reminder-card h3 {
    margin-bottom: 16px;
    font-size: 18px;
}

.form-group {
    margin-bottom: 18px;
}

.form-label {
    display: block;
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    min-width: 0;
    padding: 14px 16px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: var(--bg-body);
    color: var(--text-primary);
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:hover,
.form-input:focus {
    outline: none;
    border-color: var(--blue-600);
    box-shadow: 0 0 0 4px rgba(59,130,246,0.08);
}

.form-input::placeholder {
    color: var(--text-faint);
}

.form-input[type="time"] {
    max-width: 240px;
}

.form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 10px;
}

.reminder-card {
    padding: 24px;
    border-radius: 20px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-card);
}

.reminder-table-wrapper {
    overflow-x: auto;
}

.reminder-table {
    width: 100%;
    border-collapse: collapse;
}

.reminder-table th,
.reminder-table td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

.reminder-table th {
    font-size: 13px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.reminder-actions {
    display: flex;
    gap: 8px;
}

.btn-edit.small,
.btn-delete.small {
    padding: 8px 12px;
    font-size: 12px;
}

.inline-form {
    display: inline;
}

.empty-state {
    text-align: center;
    padding: 28px 20px;
}

.empty-state svg {
    width: 60px;
    height: 60px;
    margin-bottom: 18px;
}

.empty-state h3 {
    margin-bottom: 8px;
}

@media (max-width: 980px) {
    .reminder-grid {
        grid-template-columns: 1fr;
    }
}

.device-table-wrapper {
    overflow-x: auto;
}

.device-table {
    width: 100%;
    border-collapse: collapse;
}

.device-table th {
    text-align: left;
    padding: 14px;
    font-size: 14px;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border);
}

.device-table td {
    padding: 16px 14px;
    border-bottom: 1px solid var(--border);
    color: var(--text-primary);
}

.device-table tr:hover {
    background: var(--nav-hover-bg);
}

.category-badge {
    background: var(--blue-100);
    color: var(--blue-600);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.reminder-chip {
    display: inline-flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px 12px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 13px;
    color: var(--text-primary);
    max-width: 220px;
}

.reminder-chip strong {
    font-weight: 700;
}

.reminder-now {
    display: inline-block;
    margin-top: 4px;
    color: var(--success-color);
    font-size: 11px;
    font-weight: 700;
}

.reminder-due {
    border-color: #10b981;
    background: rgba(16,185,129,0.1);
}

.reminder-none {
    color: var(--text-muted);
    font-size: 13px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn-edit {
    background: #10b981;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

.btn-edit:hover {
    opacity: 0.9;
}

.btn-delete {
    background: #ef4444;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
}

.btn-delete:hover {
    opacity: 0.9;
}

.empty-state h3 {
    color: var(--text-primary);
}

.empty-state p {
    color: var(--text-muted);
}

.notification-container {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    z-index: 999;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-width: 400px;
    pointer-events: none;
}

.notification-toast {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: white;
    border-radius: 10px;
    border-left: 4px solid #10b981;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    animation: slideIn 0.3s ease-out;
    pointer-events: auto;
    font-size: 0.9rem;
    line-height: 1.4;
}

.notification-toast.success {
    border-left-color: #10b981;
    background: rgba(16, 185, 129, 0.02);
}

.notification-toast.warning {
    border-left-color: #f59e0b;
    background: rgba(245, 158, 11, 0.02);
}

.notification-toast.error {
    border-left-color: #ef4444;
    background: rgba(239, 68, 68, 0.02);
}

.notification-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
}

.notification-toast.success .notification-icon {
    color: #10b981;
}

.notification-toast.warning .notification-icon {
    color: #f59e0b;
}

.notification-toast.error .notification-icon {
    color: #ef4444;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.notification-message {
    color: var(--text-secondary);
}

.notification-close {
    flex-shrink: 0;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-faint);
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-close:hover {
    color: var(--text-primary);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(400px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(400px);
    }
}

.notification-toast.removing {
    animation: slideOut 0.3s ease-out forwards;
}

[data-theme="dark"] .notification-toast {
    background: #1a2235;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

[data-theme="dark"] .notification-toast.success {
    background: rgba(16, 185, 129, 0.1);
}

[data-theme="dark"] .notification-toast.warning {
    background: rgba(245, 158, 11, 0.1);
}

[data-theme="dark"] .notification-toast.error {
    background: rgba(239, 68, 68, 0.1);
}

</style>
@endsection

@section('scripts')
<script>
    // Notification System
    class NotificationSystem {
        constructor() {
            this.container = document.getElementById('notification-container');
            this.notifications = new Map();
        }

        show(message, type = 'success', duration = 5000, title = null) {
            const id = Date.now();
            const toast = document.createElement('div');
            toast.className = `notification-toast ${type}`;
            
            const iconMap = {
                success: '✓',
                warning: '⚠',
                error: '✕'
            };

            const finalTitle = title || (type === 'success' ? 'Success' : type === 'warning' ? 'Warning' : 'Error');

            toast.innerHTML = `
                <div class="notification-icon">${iconMap[type] || '•'}</div>
                <div class="notification-content">
                    <div class="notification-title">${finalTitle}</div>
                    <div class="notification-message">${message}</div>
                </div>
                <button class="notification-close" onclick="this.closest('.notification-toast').remove()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            `;

            this.container.appendChild(toast);
            this.notifications.set(id, toast);

            if (duration > 0) {
                setTimeout(() => {
                    if (this.notifications.has(id)) {
                        toast.classList.add('removing');
                        setTimeout(() => {
                            toast.remove();
                            this.notifications.delete(id);
                        }, 300);
                    }
                }, duration);
            }

            return id;
        }
    }

    const notifications = new NotificationSystem();

    // Check for due reminders
    function checkAndNotifyReminders() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const currentTime = `${hh}:${mm}`;

        const reminders = document.querySelectorAll('.reminder-chip');
        reminders.forEach(reminder => {
            const reminderTime = reminder.dataset.reminderTime;
            const reminderMessage = reminder.dataset.reminderMessage;

            if (!reminderTime) return;
            if (reminderTime !== currentTime) return;
            if (reminder.dataset.notified === 'true') return;

            reminder.dataset.notified = 'true';

            const deviceName = reminder.closest('tr')?.querySelector('td:nth-child(2)')?.textContent.trim() || 'Device';
            notifications.show(
                reminderMessage || `Time to check ${deviceName}!`,
                'success',
                6000,
                'Reminder Alert'
            );
        });
    }

    // Initialize tab system
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('.tab-panel');

        function activateTab(tabId) {
            buttons.forEach(button => {
                button.classList.toggle('active', button.dataset.tab === tabId);
            });
            panels.forEach(panel => {
                panel.classList.toggle('active', panel.id === tabId);
            });
        }

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                activateTab(this.dataset.tab);
            });
        });

        const initialTab = '{{ old('device_id') || old('reminder_time') ? 'device-reminder' : 'device-list' }}';
        activateTab(initialTab);

        // Persist reminders to localStorage so global notifier (layouts.app) can work on all pages
        function persistRemindersToStorage() {
            try {
                const reminderChips = document.querySelectorAll('.reminder-chip[data-reminder-time]');
                const reminders = [];

                reminderChips.forEach(chip => {
                    const reminderTime = chip.dataset.reminderTime;
                    const reminderMessage = chip.dataset.reminderMessage || '';
                    if (!reminderTime) return;

                    // deviceName: based on table column 2
                    const deviceName = chip.closest('tr')?.querySelector('td:nth-child(2)')?.textContent.trim() || '';

                    // create stable-ish id
                    const id = chip.dataset.deviceId || `${deviceName}-${reminderTime}-${reminderMessage}`.replace(/\s+/g,'');

                    reminders.push({
                        id,
                        time: reminderTime,
                        message: reminderMessage,
                        deviceName
                    });
                });

                localStorage.setItem('voltwise-reminders-due', JSON.stringify(reminders));
            } catch (e) {
                // ignore
            }
        }

        // Persist reminders before initial check
        persistRemindersToStorage();

        // Check for reminders initially
        checkAndNotifyReminders();


        // Check for reminders every 30 seconds
        setInterval(checkAndNotifyReminders, 30000);
    });
</script>
@endsection