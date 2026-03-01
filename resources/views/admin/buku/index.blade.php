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

    {{-- Awal Form Filter --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form action="/admin/buku" method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Judul, Penulis..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="penulis" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Penulis</option>
                            @foreach($list_penulis as $p)
                                <option value="{{ $p }}" {{ request('penulis') == $p ? 'selected' : '' }}>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="tahun_terbit" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach($list_tahun as $t)
                                <option value="{{ $t }}" {{ request('tahun_terbit') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Data</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <a href="/admin/buku" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                            <td>{{ $buku->firstItem() + $index }}</td>
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
                <div class="d-flex justify-content-end mt-3">
                    {{ $buku->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection