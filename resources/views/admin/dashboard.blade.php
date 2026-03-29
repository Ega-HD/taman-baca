@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mt-2 mb-4 fw-bold">Dashboard Statistik</h3>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people me-2"></i>Member Terdaftar</h5>
                    <h1 class="display-5 fw-bold">{{ $totalmember }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-dark shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-collection me-2"></i>Total Katalog</h5>
                    <h1 class="display-5 fw-bold">{{ $totalKatalog }}</h1>
                </div>
            </div>
        </div>
      
        <div class="col-md-4">
            <div class="card text-dark bg-info shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-journals me-2"></i>Total Buku Fisik</h5>
                    <h1 class="display-5 fw-bold">{{ $totalBukuFisik }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-bookmark-check me-2"></i>Fisik Tersedia</h5>
                    <h1 class="display-5 fw-bold">{{ $totalBukuFisikTersedia }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-dark bg-warning shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-box-arrow-up-right me-2"></i>Sedang Dipinjam</h5>
                    <h1 class="display-5 fw-bold">{{ $bukuDipinjam }}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-danger shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-hourglass-split me-2"></i>Antrian Peminjaman</h5>
                    <h1 class="display-5 fw-bold">{{ $bukuDibooking }}</h1>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-secondary shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-arrow-return-left me-2"></i>Antrian Pengembalian</h5>
                    <h1 class="display-5 fw-bold">{{ $bukuPengembalian }}</h1>
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
                        Gunakan menu navigasi di sebelah kiri untuk mengelola inventaris buku, memproses transaksi peminjaman dan pengembalian, mengubah tarif denda, serta mengelola data member PAUD Terpadu Assyfa.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection