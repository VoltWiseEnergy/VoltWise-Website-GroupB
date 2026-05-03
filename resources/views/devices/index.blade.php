@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Devices</h2>

    @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('devices.create') }}">Add New Device</a>

    <table border="1" cellpadding="10">
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
            @forelse($devices as $device)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $device->name }}</td>
                    <td>{{ $device->wattage }} W</td>
                    <td>{{ $device->category }}</td>
                    <td>
                        <a href="{{ route('devices.edit', $device->id) }}">
                            Edit
                        </a>

                        <form action="{{ route('devices.destroy', $device->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button type="submit" onclick="return confirm('Are you sure you want to delete this device?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No devices added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection