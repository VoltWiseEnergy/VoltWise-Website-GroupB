@extends('layouts.app')

@section('content')
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
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($devices as $device)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $device->name }}</td>
                                <td>{{ $device->wattage }} W</td>
                                <td>
                                    <span class="category-badge">
                                        {{ $device->category }}
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="{{ route('devices.edit', $device->id) }}" class="btn-edit">
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
@endsection


@section('styles')
<style>
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
</style>
@endsection