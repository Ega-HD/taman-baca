@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mt-2 mb-4 fw-bold">Dashboard Statistik</h3>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-book"></i> Total Katalog</h5>
                    <h1 class="display-5 fw-bold">{{ $totalKatalog }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people"></i> Member Terdaftar</h5>
                    <h1 class="display-5 fw-bold">{{ $totalPengunjung }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning shadow-sm border-0 rounded-3">
                <div class="card-body text-dark">
                    <h5 class="card-title"><i class="bi bi-arrow-left-right"></i> Sedang Dipinjam</h5>
                    <h1 class="display-5 fw-bold">{{ $bukuDipinjam }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Selamat Datang di Panel Admin!</h5>
                    <p class="card-text text-muted">
                        Gunakan menu navigasi di sebelah kiri untuk mengelola inventaris buku, mencatat donasi masuk, memproses transaksi peminjaman dan pengembalian, serta mengelola data pengunjung PAUD Terpadu Assyfa.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection