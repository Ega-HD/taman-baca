@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-4">Profil Saya</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <form action="/member/profile" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="{{ $user->nama_lengkap }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. HP (WA)</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ $user->no_hp }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" placeholder="contoh@email.com">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ $user->tempat_lahir }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" class="form-control" value="{{ $user->tgl_lahir }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ $user->alamat }}</textarea>
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold text-primary mb-3">Ubah Password</h5>
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle"></i> Kosongkan jika tidak ingin mengubah password.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ulangi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection