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
                            <th>Buku & Kode Fisik</th>
                            <th>Detail Waktu & Persetujuan</th>
                            <th>Status & Denda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                            @php
                                $sekarang = \Carbon\Carbon::now();
                                $isLate = false;
                                if ($item->deadline) {
                                    $deadline = \Carbon\Carbon::parse($item->deadline);
                                    $isLate = $sekarang->startOfDay()->gt($deadline->startOfDay());
                                }
                            @endphp
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $item->user->nama_lengkap }}</span><br>
                                <small class="text-muted"><i class="bi bi-telephone"></i> {{ $item->user->no_hp }}</small>
                            </td>
                            
                            <td>
                                <span class="fw-bold text-primary">{{ $item->itemBuku->buku->judul_buku }}</span><br>
                                <span class="badge bg-dark mt-1"><i class="bi bi-upc-scan"></i> {{ $item->itemBuku->kode_buku }}</span>
                            </td>
                            
                            <td>
                                <small class="text-muted">Diajukan: {{ \Carbon\Carbon::parse($item->tgl_pengajuan)->format('d M Y, H:i') }} WIB</small><br>
                                
                                @if($item->status == 'Menunggu Persetujuan')
                                    <small class="text-warning fw-bold"><i class="bi bi-hourglass-split"></i> Belum disetujui</small>
                                @else
                                    <small class="text-success"><i class="bi bi-check2-all"></i> Di-ACC: {{ \Carbon\Carbon::parse($item->tgl_disetujui)->format('d M Y, H:i') }} WIB</small><br>
                                    <small class="text-muted">Oleh: <strong>{{ $item->admin->nama_lengkap ?? 'Admin' }}</strong></small><br>
                                    <span class="text-danger fw-bold mt-1 d-block"><i class="bi bi-calendar-x"></i> Deadline: {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}</span>
                                @endif
                            </td>
                            
                            <td>
                                @if($item->status == 'Menunggu Persetujuan')
                                    <span class="badge bg-secondary">Menunggu ACC</span>
                                @elseif($isLate)
                                    <span class="badge bg-danger mb-1">Terlambat {{ (int) $deadline->startOfDay()->diffInDays($sekarang->startOfDay()) }} hari</span><br>
                                    <small class="text-danger fw-bold">Denda berjalan!</small>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                            
                            <td>
                                @if($item->status == 'Menunggu Persetujuan')
                                    <form action="/admin/transaksi/{{ $item->id }}/setujui" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-bold w-100" onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                @elseif($item->status == 'Sedang Dipinjam')
                                    <form action="/admin/transaksi/{{ $item->id }}/kembali" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary fw-bold w-100" onclick="return confirm('Proses pengembalian buku ini?')">
                                            <i class="bi bi-arrow-return-left"></i> Dikembalikan
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada antrean peminjaman buku saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection