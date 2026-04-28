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

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Wattage</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masterDevices as $i => $device)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="font-weight:600; color:var(--text-primary);">
                                {{ $device->name }}
                            </td>
                            <td>
                                <span class="badge badge-blue">{{ $device->category }}</span>
                            </td>
                            <td>{{ number_format($device->wattage, 0) }} W</td>
                            <td style="max-width:200px; overflow:hidden;
                                       text-overflow:ellipsis; white-space:nowrap;">
                                {{ $device->description ?? '—' }}
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.master-devices.edit', $device) }}"
                                       class="btn-secondary btn-sm">Edit</a>
                                    <form method="POST"
                                          action="{{ route('admin.master-devices.destroy', $device) }}"
                                          onsubmit="return confirm('Delete this device?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem;
                                                   color:var(--text-faint);">
                                No master devices yet. Click "Add Device" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
