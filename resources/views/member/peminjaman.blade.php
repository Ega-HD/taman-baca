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
                        <th class="ps-4">Judul Buku</th>
                        <th>Kode Fisik</th>
                        <th>Tanggal Pinjam</th>
                        <th>Deadline Pengembalian</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $item)
                    <tr>
                        <td class="ps-4 fw-bold">
                            {{ $item->itemBuku->buku->judul_buku }}
                        </td>
                        <td>
                            <span class="badge bg-dark fs-6">{{ $item->itemBuku->kode_buku }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</td>
                        <td>
                            <span class="text-danger fw-bold">
                                {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $item->status == 'Sedang Dipinjam' ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td>
                            @if($item->status == 'Dikembalikan')
                                Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                            @elseif($item->status == 'Menunggu Persetujuan')
                                <small class="text-muted">Menunggu Admin</small>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
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