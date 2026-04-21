<h3>Profile Picture</h3>

<form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="avatar">
    <button type="submit">Upload</button>
</form>

<h3>Change Password</h3>

<form method="POST" action="{{ route('profile.password') }}">
    @csrf
    <input type="password" name="password" placeholder="New Password">
    <button type="submit">Change Password</button>
</form>