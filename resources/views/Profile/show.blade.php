@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container">

    <h2>My Profile</h2>

    <div style="background: #f5f7fb; padding:20px; border-radius:10px; max-width:500px;">
        
        {{-- Avatar --}}
        <div style="text-align:center; margin-bottom:20px;">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" 
                     style="width:100px; height:100px; border-radius:50%;">
            @else
                <div style="width:100px; height:100px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-size:30px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>

        {{-- Info --}}
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone ?? '-' }}</p>

    </div>

</div>
@endsection