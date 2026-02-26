@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mt-2 mb-4 fw-bold">Tambah Buku Baru</h3>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form action="/admin/buku" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul_buku') is-invalid @enderror" name="judul_buku" value="{{ old('judul_buku') }}" required placeholder="Contoh: Mengenal Hewan Laut">
                        @error('judul_buku') <small class="text-danger">{{ $message }}</small> @enderror
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
                        <label class="form-label fw-bold">Penulis</label>
                        <input type="text" class="form-control" name="penulis" value="{{ old('penulis') }}" placeholder="Opsional">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Penerbit</label>
                        <input type="text" class="form-control" name="penerbit" value="{{ old('penerbit') }}" placeholder="Opsional">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tahun Terbit</label>
                        <input type="number" class="form-control" name="tahun_terbit" value="{{ old('tahun_terbit') }}" placeholder="Contoh: 2023">
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