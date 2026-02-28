@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h3 class="text-center fw-bold mb-4">Login Akun</h3>

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">Masuk</button>
                </form>
                
                <div class="text-center mt-3">
                    <small>Belum punya akun? <a href="/register" class="text-decoration-none fw-bold">Daftar di sini</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection