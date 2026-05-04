@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Device</h2>

    @if($errors->any())
        <div>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('devices.update', $device->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>Device Name</label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name', $device->name) }}"
                required
            >
        </div>

        <div>
            <label>Wattage</label>
            <input 
                type="number" 
                name="wattage" 
                value="{{ old('wattage', $device->wattage) }}"
                required
            >
        </div>

        <div>
            <label>Category</label>
            <input 
                type="text" 
                name="category" 
                value="{{ old('category', $device->category) }}"
                required
            >
        </div>

        <br>

        <button type="submit">Update Device</button>
        <a href="{{ route('devices.index') }}">Cancel</a>
    </form>
</div>
@endsection