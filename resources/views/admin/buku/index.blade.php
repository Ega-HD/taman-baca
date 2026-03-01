@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
        <h3 class="fw-bold">Kelola Data Buku</h3>
        <a href="/admin/buku/create" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-lg"></i> Tambah Buku Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ $errors->first('error') }} 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Penulis & Penerbit</th>
                            <th>Tahun</th>
                            <th>Total Stok (Fisik)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buku as $index => $katalog)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $katalog->judul_buku }}</td>
                            <td>
                                <small>Penulis: {{ $katalog->penulis }}</small><br>
                                <small class="text-muted">Penerbit: {{ $katalog->penerbit }}</small>
                            </td>
                            <td>{{ $katalog->tahun_terbit }}</td>
                            <td>
                                <span class="badge bg-info text-dark fs-6">
                                    {{ $katalog->itemBuku->count() }} Buku
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ $katalog->itemBuku->where('status_buku', 'Tersedia')->count() }} Tersedia
                                </small>
                            </td>
                            <td>
                                <a href="/admin/buku/{{ $katalog->id }}" class="btn btn-sm btn-info text-white mb-1" title="Lihat Daftar Kode Buku Fisik"><i class="bi bi-eye"></i></a>
                                <a href="/admin/buku/{{ $katalog->id }}/edit" class="btn btn-sm btn-warning mb-1"><i class="bi bi-pencil"></i></a>
                                <form action="/admin/buku/{{ $katalog->id }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('PERINGATAN KERAS:\nMenghapus katalog ini akan menghapus SEMUA {{ $katalog->itemBuku->count() }} salinan fisik dan riwayat peminjamannya.\n\nApakah Anda yakin?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data buku.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection