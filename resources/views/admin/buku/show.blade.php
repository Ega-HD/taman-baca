@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
        <div>
            <a href="/admin/buku" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Buku</a>
            <h3 class="fw-bold mt-2">Detail Katalog Buku</h3>
        </div>
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#tambahFisikModal">
            <i class="bi bi-plus-lg"></i> Tambah Salinan Fisik
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->has('error'))
        <div class="alert alert-danger">{{ $errors->first('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <h4 class="fw-bold text-primary">{{ $buku->judul_buku }}</h4>
            <div class="row mt-3">
                <div class="col-md-3"><p class="text-muted mb-1">Penulis</p><h6 class="fw-bold">{{ $buku->penulis }}</h6></div>
                <div class="col-md-3"><p class="text-muted mb-1">Penerbit</p><h6 class="fw-bold">{{ $buku->penerbit }}</h6></div>
                <div class="col-md-3"><p class="text-muted mb-1">Tahun Terbit</p><h6 class="fw-bold">{{ $buku->tahun_terbit }}</h6></div>
                <div class="col-md-3"><p class="text-muted mb-1">Total Salinan Fisik</p><h6 class="fw-bold"><span class="badge bg-info text-dark fs-6">{{ $buku->itemBuku->count() }} Buku</span></h6></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold">Daftar Kode Buku Fisik</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Buku Unik</th>
                            <th>Status Peminjaman</th>
                            <th>Tgl Masuk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buku->itemBuku as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-dark fs-6">{{ $item->kode_buku }}</span></td>
                            <td>
                                <span class="badge {{ $item->status_buku == 'Tersedia' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $item->status_buku }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_ditambahkan)->format('d M Y') }}</td>
                            <td>
                                @if($item->status_buku == 'Tersedia')
                                    <form action="/admin/item-buku/{{ $item->id }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Hapus salinan fisik dengan kode {{ $item->kode_buku }}? Data tidak bisa dikembalikan.')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Sedang dipinjam"><i class="bi bi-trash"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada salinan fisik untuk buku ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahFisikModal" tabindex="-1" aria-labelledby="tambahFisikModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="tambahFisikModalLabel">Tambah Salinan Fisik Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/buku/{{ $buku->id }}/tambah-fisik" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Kode buku unik akan dibuat secara otomatis (melanjutkan kode terakhir di sistem).</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Salinan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah_buku" min="1" value="1" required>
                    </div>
                    
                    {{-- <div class="mb-3">
                        <label class="form-label fw-bold">Asal Buku <span class="text-danger">*</span></label>
                        <select class="form-select" name="asal_buku" required>
                            <option value="Baru">Pengadaan Baru (Beli)</option>
                            <option value="Donasi">Hasil Donasi</option>
                        </select>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Stok Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection