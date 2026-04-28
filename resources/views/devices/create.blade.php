@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Device</h2>

    @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('devices.store') }}" method="POST">
        @csrf

        <div>
            <label>Device Name</label>
            <input type="text" name="name" value="{{ old('name') }}">
        </div>

        <div>
            <label>Wattage</label>
            <input type="number" name="wattage" value="{{ old('wattage') }}">
        </div>

        <div>
            <label>Category</label>
            <input type="text" name="category" value="{{ old('category') }}">
        </div>

        <button type="submit">Add Device</button>
    </form>
</div>
@endsection