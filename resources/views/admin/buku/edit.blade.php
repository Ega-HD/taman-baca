@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mt-2 mb-4 fw-bold">Edit Katalog Buku</h3>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form action="/admin/buku/{{ $buku->id }}" method="POST">
                @csrf
                @method('PUT') <div class="mb-3">
                    <label class="form-label fw-bold">Judul Buku</label>
                    <input type="text" class="form-control" name="judul_buku" value="{{ $buku->judul_buku }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Penulis</label>
                        <input type="text" class="form-control" name="penulis" value="{{ $buku->penulis }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Penerbit</label>
                        <input type="text" class="form-control" name="penerbit" value="{{ $buku->penerbit }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tahun Terbit</label>
                        <input type="number" class="form-control" name="tahun_terbit" value="{{ $buku->tahun_terbit }}" required>
                    </div>
                </div>

                <div class="alert alert-warning small">
                    <i class="bi bi-info-circle"></i> Mengedit data ini tidak akan mengubah Kode Buku Fisik yang sudah tercetak.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/admin/buku" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning fw-bold">Update Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection