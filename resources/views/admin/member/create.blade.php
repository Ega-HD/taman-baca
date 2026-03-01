@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mt-2 mb-4 fw-bold">Tambah Member Baru</h3>

    <div class="card shadow-sm border-0 rounded-3 col-md-8">
        <div class="card-body p-4">
            <form action="/admin/members" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. HP (WA)</label>
                        <input type="text" name="no_hp" class="form-control" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipe Akun (Role)</label>
                    <select name="role" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Role --</option>
                        <option value="member">Member (Peminjam)</option>
                        <option value="admin">Admin (Pengelola)</option>
                    </select>
                    <small class="text-muted">Admin memiliki akses penuh ke sistem, Member hanya bisa meminjam.</small>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Password Awal</label>
                    <input type="text" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/admin/members" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection