{{-- @extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold mt-2 mb-4">Pengaturan Sistem</h3>

    <div class="card shadow-sm border-0 rounded-3 col-md-6">
        <div class="card-body">
            <form action="/admin/pengaturan" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Tarif Denda Keterlambatan (Per Hari)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" name="denda_per_hari" value="{{ $pengaturan->denda_per_hari }}" required>
                    </div>
                    <small class="text-muted">Nominal ini akan digunakan untuk menghitung denda buku yang terlambat.</small>
                </div>
                <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold mt-2 mb-4">Pengaturan Sistem & Laporan</h3>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold"><i class="bi bi-gear"></i> Pengaturan Tarif Denda</h6>
                </div>
                <div class="card-body">
                    <form action="/admin/pengaturan" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tarif Denda Keterlambatan (Per Hari)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="denda_per_hari" value="{{ $pengaturan->denda_per_hari }}" required>
                            </div>
                            <small class="text-muted">Nominal ini akan digunakan untuk menghitung denda otomatis saat buku terlambat dikembalikan.</small>
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 bg-primary text-white">
                <div class="card-header border-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="bi bi-wallet2"></i> Total Pemasukan Denda</h6>
                    
                    <form action="/admin/pengaturan" method="GET" class="m-0">
                        <select name="filter_tahun" class="form-select form-select-sm fw-bold text-dark" onchange="this.form.submit()">
                            <option value="semua" {{ $filterTahun == 'semua' ? 'selected' : '' }}>Keseluruhan (Reset)</option>
                            <option value="tahun_ini" {{ $filterTahun == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini ({{ now()->year }})</option>
                            <option value="tahun_kemarin" {{ $filterTahun == 'tahun_kemarin' ? 'selected' : '' }}>Tahun Kemarin ({{ now()->subYear()->year }})</option>
                        </select>
                    </form>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <p class="mb-1 text-white-50 fw-bold">
                        @if($filterTahun == 'tahun_ini')
                            Akumulasi Pemasukan Selama Tahun {{ now()->year }}
                        @elseif($filterTahun == 'tahun_kemarin')
                            Akumulasi Pemasukan Selama Tahun {{ now()->subYear()->year }}
                        @else
                            Akumulasi Pemasukan Keseluruhan Waktu
                        @endif
                    </p>
                    <h1 class="display-5 fw-bold mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection