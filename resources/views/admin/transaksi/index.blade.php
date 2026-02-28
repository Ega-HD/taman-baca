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
                            <th>No</th>
                            <th>Peminjam</th>
                            <th>Buku & Kode Fisik</th>
                            <th>Detail Waktu & Persetujuan</th>
                            <th>Status & Denda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $key => $item)
                        <tr>
                            <td>
                                {{ ($key++) + 1 }}
                            </td>
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
                                    @if($item->status != 'Sedang Dipinjam')
                                        <small class="text-primary">
                                            <i class="bi bi-clock-history"></i> Diajukan kembali:
                                            {{ \Carbon\Carbon::parse($item->tgl_pengajuan_kembali)->format('d M Y, H:i') }} WIB
                                        </small>
                                    @endif
                                    <span class="text-danger fw-bold mt-1 d-block"><i class="bi bi-calendar-x"></i> Deadline: {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}</span>
                                @endif
                            </td>
                            
                            <td>
                                @php
                                    $sekarang = \Carbon\Carbon::now();
                                    $dendaBerjalan = 0;
                                    $hariTerlambat = 0;

                                    // Jika transaksi belum selesai (masih dipinjam/menunggu kembali) DAN sudah lewat deadline
                                    if (in_array($item->status, ['Sedang Dipinjam', 'Menunggu Pengembalian']) && $item->deadline) {
                                        $deadline = \Carbon\Carbon::parse($item->deadline);
                                        if ($sekarang->startOfDay()->gt($deadline->startOfDay())) {
                                            $hariTerlambat = (int) $deadline->startOfDay()->diffInDays($sekarang->startOfDay());
                                            $dendaBerjalan = $hariTerlambat * $tarifDenda; // Gunakan tarif dari database
                                        }
                                    }
                                @endphp

                                @if($item->status == 'Menunggu Persetujuan')
                                    <span class="badge bg-secondary">Menunggu ACC Pinjam</span>
                                
                                @elseif($item->status == 'Sedang Dipinjam')
                                    <span class="badge bg-info text-dark mb-1"><i class="bi bi-book"></i> Sedang Dipinjam</span><br>
                                    <small class="text-muted">Belum diajukan kembali</small><br>
                                    @if($hariTerlambat > 0)
                                        <span class="badge bg-danger mb-1">Terlambat {{ $hariTerlambat }} Hari</span><br>
                                        <small class="text-danger fw-bold">
                                            Estimasi Denda: Rp {{ number_format($dendaBerjalan, 0, ',', '.') }}
                                        </small>
                                        <div class="text-muted small fst-italic mt-1">(Tarif: Rp {{ number_format($tarifDenda,0) }}/hari)</div>
                                    @else
                                        <span class="badge bg-success">Aman</span>
                                    @endif
                                
                                @elseif($item->status == 'Menunggu Pengembalian')
                                    <span class="badge bg-warning text-dark mb-1"><i class="bi bi-bell-fill"></i> Diajukan Kembali</span><br>
                                    <small class="text-primary fw-bold">Menunggu Proses Anda</small><br>
                                    @if($hariTerlambat > 0)
                                        <span class="badge bg-danger mb-1">Terlambat {{ $hariTerlambat }} Hari</span><br>
                                        <small class="text-danger fw-bold">
                                            Estimasi Denda: Rp {{ number_format($dendaBerjalan, 0, ',', '.') }}
                                        </small>
                                        <div class="text-muted small fst-italic mt-1">(Tarif: Rp {{ number_format($tarifDenda,0) }}/hari)</div>
                                    @else
                                        <span class="badge bg-success">Aman</span>
                                    @endif
                                
                                @elseif($item->status == 'Dikembalikan')
                                    <small class="text-muted d-block mb-1">Kembali: {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y, H:i') }}</small>
                                    
                                    @if($item->total_denda > 0)
                                        <small class="text-danger d-block mb-1">Terlambat: {{ $item->hari_telat }} hari</small>
                                        <small class="text-danger fw-bold">
                                            Estimasi Denda: Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                        </small>
                                        <div class="text-muted small fst-italic mt-1">(Tarif: Rp {{ number_format($tarifDenda,0) }}/hari)</div>

                                        @if($item->tgl_pelunasan) 
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Transaksi Selesai</span>
                                        @endif
                                    @else
                                        <small class="text-success fw-bold d-block mb-1"><i class="bi bi-check-lg"></i> Tepat Waktu</small>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Transaksi Selesai</span>
                                    @endif
                                
                                @elseif($item->status == 'Menunggu Pengembalian')
                                    <div class="mt-2 text-primary small">
                                        <i class="bi bi-clock-history"></i> Diajukan: <br>
                                        {{ \Carbon\Carbon::parse($item->tgl_pengajuan_kembali)->format('d M Y, H:i') }}
                                    </div>
                                {{-- @elseif($isLate)
                                    <span class="badge bg-danger mb-1">Terlambat {{ (int) $deadline->startOfDay()->diffInDays($sekarang->startOfDay()) }} hari</span><br>
                                    <small class="text-danger fw-bold">Denda berjalan!</small> --}}
                                @endif
                            </td>
                            
                            <td>
                                @if($item->status == 'Menunggu Persetujuan')
                                    <form action="/admin/transaksi/{{ $item->id }}/setujui" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-bold w-100" onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="bi bi-check-lg"></i> Setujui Pinjam
                                        </button>
                                    </form>
                                
                                @elseif($item->status == 'Menunggu Pengembalian' || $item->status == 'Sedang Dipinjam')
                                    <form action="/admin/transaksi/{{ $item->id }}/kembali" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $item->status == 'Menunggu Pengembalian' ? 'btn-primary' : 'btn-outline-secondary' }} fw-bold w-100" onclick="return confirm('Terima fisik buku dan proses pengembalian?')">
                                            <i class="bi bi-arrow-return-left"></i> Terima Buku
                                        </button>
                                    </form>
                                
                                @elseif($item->status == 'Dikembalikan' && $item->total_denda > 0 && !$item->tgl_pelunasan)
                                    <form action="/admin/transaksi/{{ $item->id }}/lunas" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning fw-bold w-100" onclick="return confirm('Konfirmasi pelunasan denda?')">
                                            <i class="bi bi-cash"></i> Lunasi Denda
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