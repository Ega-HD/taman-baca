@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mt-2 mb-4 fw-bold">Edit Data Member</h3>

    <div class="card shadow-sm border-0 rounded-3 col-md-8">
        <div class="card-body p-4">
            <form action="/admin/members/{{ $member->id }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ $member->nama_lengkap }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ $member->username }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. HP (WA)</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ $member->no_hp }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email (Opsional)</label>
                        <input type="email" name="email" class="form-control" value="{{ $member->email }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ $member->tempat_lahir }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-control" value="{{ $member->tgl_lahir }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ $member->alamat }}</textarea>
                </div>
                
                <div class="alert alert-info py-2">
                    <i class="bi bi-info-circle"></i> Kosongkan kolom password jika tidak ingin mengubah password member.
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Reset Password Baru</label>
                    <input type="text" name="password" class="form-control" placeholder="Isi hanya jika ingin mereset password">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/admin/members" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning fw-bold">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection