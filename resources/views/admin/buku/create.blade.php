@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mt-2 mb-4 fw-bold">Tambah Buku Baru</h3>

    @if($errors->has('error'))
        <div class="alert alert-danger">{{ $errors->first('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form action="/admin/buku" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul_buku" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Asal Buku <span class="text-danger">*</span></label>
                        <select class="form-select" name="asal_buku" required>
                            <option value="Baru">Pengadaan Baru (Beli)</option>
                            <option value="Donasi">Hasil Donasi</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Penulis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="penulis" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Penerbit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="penerbit" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tahun Terbit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="tahun_terbit" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Jumlah Eksemplar/Fisik <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah_buku" min="1" value="1" required>
                        <small class="text-muted">Berapa banyak salinan buku ini yang ditambahkan?</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/admin/buku" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection