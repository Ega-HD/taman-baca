@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h2 class="fw-bold">Koleksi Buku Taman Baca</h2>
        <p class="text-muted">PAUD Terpadu Assyfa</p>
    </div>
</div>

<div class="row g-4">
    @foreach($buku as $katalog)
    @php
        // Menghitung berapa salinan fisik yang statusnya masih 'Tersedia'
        $stokTersedia = $katalog->itemBuku->where('status_buku', 'Tersedia')->count();
    @endphp

    <div class="col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mt-1">{{ $katalog->judul_buku }}</h5>
                <p class="card-text text-muted mb-1">
                    <small>Penulis: {{ $katalog->penulis }}</small><br>
                    <small>Penerbit: {{ $katalog->penerbit }} ({{ $katalog->tahun_terbit }})</small>
                </p>

                @auth
                    @if(Auth::user()->role == 'pengunjung')
                        <div class="mt-3">
                            @if($stokTersedia > 0)
                                <span class="badge bg-success mb-2">Tersedia</span>
                            @else
                                <span class="badge bg-danger mb-2">Sedang Dipinjam Semua</span>
                            @endif
                        </div>
                    @endif
                @endauth
                </div>

            @auth
                @if(Auth::user()->role == 'pengunjung')
                    <div class="card-footer bg-white border-0 pb-3">
                        <button class="btn btn-outline-primary w-100 btn-sm" {{ $stokTersedia == 0 ? 'disabled' : '' }}>
                            {{ $stokTersedia > 0 ? 'Pinjam Buku Ini' : 'Stok Kosong' }}
                        </button>
                    </div>
                @endif
            @endauth
            
            @guest
                <div class="card-footer bg-white border-0 pb-3 text-center">
                    <small class="text-muted"><a href="/login">Login</a> untuk meminjam buku.</small>
                </div>
            @endguest
        </div>
    </div>
    @endforeach
</div>
@endsection