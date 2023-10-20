@extends('main')
@section('title', 'login')
@section('content')
<form method="post" class="p-5 border rounded">
    <h1>Login</h1>
    <input type="text" class="form-control mb-3" name="login" id="login" placeholder="Username or Email address">
    <input type="password" name="password" class="form-control mb-3" id="Password" placeholder="Password">
    <button class="btn btn-primary d-block mx-auto" type="submit">Login</button>
</form>
@endSection