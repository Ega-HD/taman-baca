@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
        <h3 class="fw-bold">Kelola Peminjaman Buku</h3>
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

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Peminjam</th>
                            <th>Judul & Kode Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Deadline</th>
                            <th>Status Keterlambatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                            @php
                                $deadline = \Carbon\Carbon::parse($item->deadline);
                                $sekarang = \Carbon\Carbon::now();
                                $isLate = $sekarang->gt($deadline);
                            @endphp
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $item->user->nama_lengkap }}</span><br>
                                <small class="text-muted">{{ $item->user->no_hp }}</small>
                            </td>
                            <td>
                                <span class="fw-bold">{{ $item->itemBuku->buku->judul_buku }}</span><br>
                                <span class="badge bg-dark">{{ $item->itemBuku->kode_buku }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</td>
                            <td>
                                <span class="{{ $isLate ? 'text-danger fw-bold' : '' }}">
                                    {{ $deadline->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                @if($item->status == 'Menunggu Persetujuan')
                                    <span class="badge bg-secondary">Menunggu ACC</span>
                                @elseif($isLate)
                                    <span class="badge bg-danger">Terlambat {{ (int) $deadline->startOfDay()->diffInDays($sekarang->startOfDay()) }} hari</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'Menunggu Persetujuan')
                                    <form action="/admin/transaksi/{{ $item->id }}/setujui" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-bold" onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                @elseif($item->status == 'Sedang Dipinjam')
                                    <form action="/admin/transaksi/{{ $item->id }}/kembali" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary fw-bold" onclick="return confirm('Proses pengembalian buku ini?')">
                                            <i class="bi bi-arrow-return-left"></i> Proses Pengembalian
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada buku yang sedang dipinjam saat ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection