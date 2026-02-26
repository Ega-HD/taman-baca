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

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Asal Buku</th>
                            <th>Status</th>
                            <th>Tgl Masuk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buku as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $item->judul_buku }}</td>
                            <td>{{ $item->penulis ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->asal_buku }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $item->status_buku == 'Tersedia' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $item->status_buku }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_ditambahkan)->format('d M Y') }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="#" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data buku.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection