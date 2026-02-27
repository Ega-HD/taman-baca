@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h2 class="fw-bold">Koleksi Buku Taman Baca</h2>
        <p class="text-muted">PAUD Terpadu Assyfa</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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
                        @if($stokTersedia > 0)
                            <form action="/member/pinjam/{{ $katalog->id }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 btn-sm fw-bold" onclick="return confirm('Pinjam buku {{ $katalog->judul_buku }} sekarang?')">
                                    Pinjam Buku Ini
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary w-100 btn-sm" disabled>
                                Stok Kosong
                            </button>
                        @endif
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