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
            </tr>
        </thead>

        <tbody>
            @forelse($devices as $device)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $device->name }}</td>
                    <td>{{ $device->wattage }} W</td>
                    <td>{{ $device->category }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No devices added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection