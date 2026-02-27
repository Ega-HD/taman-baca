@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold">Buku Saya (Riwayat Peminjaman)</h3>
        <p class="text-muted">Daftar buku yang sedang dan pernah Anda pinjam di Taman Baca.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Buku & Kode Fisik</th>
                        <th>Status</th>
                        <th>Riwayat Waktu</th>
                        <th>Persetujuan</th>
                        <th>Tagihan Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $item)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold d-block mb-1">{{ $item->itemBuku->buku->judul_buku }}</span>
                            <span class="badge bg-dark">{{ $item->itemBuku->kode_buku }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $item->status == 'Sedang Dipinjam' ? 'bg-warning text-dark' : ($item->status == 'dikembalikan' ? 'bg-success' : 'bg-secondary') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted d-block">Diajukan: {{ \Carbon\Carbon::parse($item->tgl_pengajuan)->format('d M Y') }}</small>
                            @if($item->status != 'Menunggu Persetujuan')
                                <small class="text-success d-block">Dipinjam: {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</small>
                                @if($item->status != 'Dikembalikan')
                                    <small class="text-danger fw-bold d-block mt-1">Deadline: {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}</small>
                                @else
                                    <small class="text-primary fw-bold d-block mt-1">Kembali: {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }}</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'Menunggu Persetujuan')
                                <small class="text-muted fst-italic">Menunggu Admin</small>
                            @else
                                <span class="fw-bold">{{ $item->admin->nama_lengkap ?? '-' }}</span><br>
                                <small class="text-muted">Admin PAUD</small>
                            @endif
                        </td>
                        <td class="fw-bold">
                            @if($item->status == 'Dikembalikan')
                                @if($item->total_denda > 0)
                                    <span class="text-danger">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success">Rp 0 (Tepat Waktu)</span>
                                @endif
                            @elseif($item->status == 'Menunggu Persetujuan')
                                <small class="text-muted">-</small>
                            @else
                                <small class="text-muted">Belum Selesai</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            Anda belum memiliki riwayat peminjaman buku. <br>
                            <a href="/" class="btn btn-outline-primary mt-3">Mulai Cari Buku</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection