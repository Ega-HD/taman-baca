@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h2 class="fw-bold">Koleksi Buku Taman Baca</h2>
        <p class="text-muted">PAUD Terpadu Assyfa</p>
    </div>
</div>

<div class="row g-4">
    @foreach($buku as $item)
    <div class="col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <span class="badge {{ $item->status_buku == 'Tersedia' ? 'bg-success' : 'bg-danger' }} mb-2">
                    {{ $item->status_buku }}
                </span>
                <span class="badge bg-info text-dark mb-2">
                    {{ $item->asal_buku }}
                </span>
                
                <h5 class="card-title fw-bold mt-1">{{ $item->judul_buku }}</h5>
                <p class="card-text text-muted mb-1">
                    <small>Penulis: {{ $item->penulis ?? 'Tidak diketahui' }}</small><br>
                    <small>Penerbit: {{ $item->penerbit ?? 'Tidak diketahui' }}</small>
                </p>
            </div>
            <div class="card-footer bg-white border-0 pb-3">
                <button class="btn btn-outline-primary w-100 btn-sm" {{ $item->status_buku == 'Dipinjam' ? 'disabled' : '' }}>
                    Pinjam Buku Ini
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection