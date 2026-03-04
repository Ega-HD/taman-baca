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

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form action="/" method="GET">
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
                        <a href="/" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
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
                        @if(Auth::user()->role == 'member')
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
                    @if(Auth::user()->role == 'member')
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
    <div class="d-flex justify-content-end mt-3">
        {{ $buku->withQueryString()->links() }}
    </div>
@endsection