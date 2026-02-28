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
                        <th>No</th>
                        <th class="ps-4">Buku & Kode Fisik</th>
                        <th>Status</th>
                        <th>Riwayat Waktu</th>
                        <th>Persetujuan Pinjam</th>
                        <th>Pengembalian Diterima</th>
                        <th>Tagihan Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $index => $item)
                    <tr>
                        {{-- No --}}
                        <td>{{ $index + 1 }}</td>

                        {{-- Judul Buku --}}
                        <td class="ps-4">
                            <span class="fw-bold d-block mb-1">{{ $item->itemBuku->buku->judul_buku }}</span>
                            <span class="badge bg-dark">{{ $item->itemBuku->kode_buku }}</span>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="badge {{ 
                                $item->status == 'Sedang Dipinjam' ? 'bg-primary' : 
                                ($item->status == 'Menunggu Pengembalian' ? 'bg-warning text-dark' : 
                                ($item->status == 'Dikembalikan' ? 'bg-success' : 'bg-secondary')) 
                            }} mb-2 d-inline-block">
                                {{ $item->status }}
                            </span>
                            @if($item->status == 'Sedang Dipinjam')
                                <form action="/member/peminjaman/{{ $item->id }}/ajukan-kembali" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('Ajukan pengembalian buku ini sekarang?')">
                                        <i class="bi bi-box-arrow-in-right"></i> Kembalikan Buku
                                    </button>
                                </form>
                            @endif
                        </td>

                        {{-- Riwayat Waktu --}}
                        <td>
                            <small class="text-muted d-block">Diajukan pinjam: {{ \Carbon\Carbon::parse($item->tgl_pengajuan)->format('d M Y, H:i') }} WIB</small>
                            @if($item->status != 'Menunggu Persetujuan')
                                <small class="text-success d-block">Dipinjam: {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y, H:i') }} WIB</small>
                                @if($item->status != 'Dipinjam')
                                    <small class="mt-2 text-primary small">
                                        Diajukan kembali:
                                        {{ \Carbon\Carbon::parse($item->tgl_pengajuan_kembali)->format('d M Y, H:i') }} WIB
                                    </small>
                                @endif
                                @if($item->status != 'Dikembalikan')
                                    <small class="text-danger fw-bold d-block mt-1">Deadline: {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}</small>
                                @else
                                    <small class="text-primary fw-bold d-block mt-1">Kembali: {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }}</small>
                                @endif
                            @endif
                        </td>

                        {{-- Persetujuan Pinjam --}}
                        <td>
                            @if($item->status == 'Menunggu Persetujuan')
                                <small class="text-muted fst-italic">Menunggu Admin</small>
                            @else
                                <span class="fw-bold">{{ $item->admin->nama_lengkap ?? '-' }}</span><br>
                                <small class="text-muted">Admin PAUD</small>
                            @endif
                        </td>

                        {{-- Diterima Kembali --}}
                        <td>
                            @if($item->status == 'Menunggu Pengembalian')
                                <small class="text-muted fst-italic">Menunggu Admin</small>
                            @elseif($item->status == 'Dikembalikan')
                                <span class="fw-bold">{{ $item->adminPengembalian->nama_lengkap ?? '-' }}</span><br>
                                <small class="text-muted">Admin PAUD</small>
                            @endif
                        </td>

                        @php
                            $sekarang = \Carbon\Carbon::now();
                            $dendaBerjalan = 0;
                            $hariTerlambat = 0;

                            // Jika transaksi belum selesai (masih dipinjam/menunggu kembali) DAN sudah lewat deadline
                            if (in_array($item->status, ['Sedang Dipinjam', 'Menunggu Pengembalian']) && $item->deadline) {
                                $tarifDenda = $item->tarif_denda_berlaku;
                                $deadline = \Carbon\Carbon::parse($item->deadline);
                                if ($sekarang->startOfDay()->gt($deadline->startOfDay())) {
                                    $hariTerlambat = (int) $deadline->startOfDay()->diffInDays($sekarang->startOfDay());
                                    $dendaBerjalan = $hariTerlambat * $tarifDenda; // Gunakan tarif dari database
                                }
                            }
                        @endphp

                        {{-- Tagihan Denda --}}
                        <td class="fw-bold">
                            @if($item->status == 'Dikembalikan')
                                @if($item->total_denda > 0)
                                    <small class="text-danger d-block mb-1">Terlambat: {{ $item->hari_telat }} hari</small>
                                    
                                    @if($item->tgl_pelunasan)
                                        <span class="text-success"><del>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</del> <br>
                                        <i class="bi bi-check-circle-fill"></i> Lunas pada {{ \Carbon\Carbon::parse($item->tgl_pelunasan)->format('d M Y, H:i') }} WIB</span>
                                    @else
                                        <span class="text-danger">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</span>
                                    @endif
                                @else
                                    <span class="text-success">Rp 0 (Tepat Waktu)</span>
                                @endif
                            @elseif($item->status == 'Menunggu Persetujuan')
                                <small class="text-muted">-</small>
                            @elseif ($item->status == 'Sedang Dipinjam' || $item->status == 'Menunggu Pengembalian')
                                @if($hariTerlambat > 0)
                                    <span class="badge bg-danger mb-1">Terlambat {{ $hariTerlambat }} Hari</span><br>
                                    <small class="text-danger fw-bold">
                                        Estimasi Denda: Rp {{ number_format($dendaBerjalan, 0, ',', '.') }}
                                    </small>
                                    <div class="text-muted small fst-italic mt-1">(Tarif: Rp {{ number_format($tarifDenda,0) }}/hari)</div>
                                @else
                                    <small class="text-muted">Belum Selesai</small>
                                @endif
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