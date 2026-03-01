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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first('error') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form action="/admin/transaksi" method="GET">
                <div class="row g-3">
                    {{-- Search Bar --}}
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama Member, Judul Buku, atau Kode..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Filter Status Transaksi --}}
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Menunggu Persetujuan" {{ request('status') == 'Menunggu Persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan Pinjam</option>
                            <option value="Sedang Dipinjam" {{ request('status') == 'Sedang Dipinjam' ? 'selected' : '' }}>Sedang Dipinjam (Aktif)</option>
                            <option value="Menunggu Pengembalian" {{ request('status') == 'Menunggu Pengembalian' ? 'selected' : '' }}>Menunggu Pengembalian (Diajukan)</option>
                            <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Selesai / Dikembalikan</option>
                            <hr>
                            <option value="terlambat" class="fw-bold text-danger" {{ request('status') == 'terlambat' ? 'selected' : '' }}>⚠️ Terlambat (Jatuh Tempo)</option>
                            <option value="denda_belum_lunas" class="fw-bold text-warning" {{ request('status') == 'denda_belum_lunas' ? 'selected' : '' }}>💰 Denda Belum Lunas</option>
                        </select>
                    </div>

                    {{-- Dropdown jumlah data per page --}}
                    <div class="col-md-2">
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Data</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="/admin/transaksi" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                                {{ $transaksi->firstItem() + $key }}
                            </td>
                            {{-- Peminjam --}}
                            <td>
                                <span class="fw-bold">{{ $item->user->nama_lengkap }}</span><br>
                                <small class="text-muted"><i class="bi bi-telephone"></i> {{ $item->user->no_hp }}</small>
                            </td>
                            
                            {{-- Buku & Kode Fisik --}}
                            <td>
                                <span class="fw-bold text-primary">{{ $item->itemBuku->buku->judul_buku }}</span><br>
                                <span class="badge bg-dark mt-1"><i class="bi bi-upc-scan"></i> {{ $item->itemBuku->kode_buku }}</span>
                            </td>
                            
                            {{-- Detail Waktu dan Persetujuan --}}
                            <td>
                                {{-- keterangan edited --}}
                                @if($item->updatedBy)
                                    <small class="badge bg-warning text-dark fst-italic" style="font-size: 0.7rem;">
                                        *Diedit: {{ $item->updatedBy->nama_lengkap }} ({{ \Carbon\Carbon::parse($item->tgl_diupdate)->format('d M Y, H:i') }} WIB)
                                    </small><br>
                                @endif
                                {{-- menunggu persetujuan pinjam --}}
                                <small class="text-muted">Diajukan pinjam: {{ \Carbon\Carbon::parse($item->tgl_pengajuan_pinjam)->format('d M Y, H:i') }} WIB</small><br>
                                @if($item->status == 'Menunggu Persetujuan')
                                    <small class="text-warning fw-bold"><i class="bi bi-hourglass-split"></i> Belum disetujui</small>
                                @elseif($item->status == 'Ditolak')
                                    <span class="badge bg-danger">Ditolak </span>
                                    <small class="text-muted">: {{ \Carbon\Carbon::parse($item->tgl_ditolak)->format('d/m/y H:i') }} WIB</small><br>
                                    <small class="text-danger">Oleh: {{ $item->rejectedBy->nama_lengkap ?? 'Admin' }}</small>
                                {{-- sedang dipinjam --}}
                                @elseif($item->status != 'Menunggu Persetujuan' || $item->status != 'Ditolak')
                                    <small class="text-success"><i class="bi bi-check2-all"></i> Di-ACC: {{ \Carbon\Carbon::parse($item->tgl_disetujui)->format('d M Y, H:i') }} WIB</small><br>
                                    <small class="text-muted">Oleh: <strong>{{ $item->approvedBy->nama_lengkap ?? 'Admin' }}</strong></small><br>
                                    {{-- dikembalikan --}}
                                    @if($item->status != 'Sedang Dipinjam')
                                        <small class="text-primary">
                                            <i class="bi bi-clock-history"></i> Diajukan pengembalian:
                                            {{ \Carbon\Carbon::parse($item->tgl_pengajuan_pengembalian)->format('d M Y, H:i') }} WIB
                                        </small><br>
                                        @if($item->status != 'Menunggu Pengembalian')
                                            <small class="fw-bold text-primary"><i class="bi bi-box-arrow-in-down"></i> Diterima Kembali: 
                                            {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y, H:i') }} WIB</small><br>
                                            <small class="text-muted">Oleh: <strong>{{ $item->retrievedBy->nama_lengkap ?? 'Admin' }}</strong></small><br>
                                        @endif
                                    @endif
                                    <span class="text-danger fw-bold mt-1 d-block"><i class="bi bi-calendar-x"></i> Deadline: {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}</span>
                                @endif
                            </td>
                            
                            {{-- Status dan Denda --}}
                            <td class="text-center">
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

                                @if ($item->status == 'Ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
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
                                    @if($item->total_denda > 0)
                                        @if($item->tgl_pelunasan)
                                            <span class="text-success fw-bold mb-1">Dikembalikan <i class="bi bi-check-lg"></i></span><br>  
                                        @else
                                            <span class="badge bg-warning text-dark mb-1"><i class="bi bi-bell-fill"></i> Dikembalikan</span><br>                                    
                                        @endif
                                        <small class="text-danger d-block mb-1">Terlambat: {{ $item->hari_telat }} hari</small>
                                        <small class="text-danger fw-bold">
                                            Estimasi Denda: Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                        </small>
                                        <div class="text-muted small fst-italic mt-1">(Tarif: Rp {{ number_format($item->tarif_denda_berlaku,0) }}/hari)</div>

                                        @if($item->tgl_pelunasan) 
                                            <span class="text-success"><del>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</del></span>
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Transaksi Selesai</span>
                                        @endif
                                    @else
                                        <small class="text-success fw-bold d-block mb-1"><i class="bi bi-check-lg"></i> Tepat Waktu</small>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Transaksi Selesai</span>
                                    @endif
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            <td>
                                @if($item->status == 'Menunggu Persetujuan')
                                    <form action="/admin/transaksi/{{ $item->id }}/setujui" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-bold w-100" onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="bi bi-check-lg"></i> Setujui Pinjam
                                        </button>
                                    </form>
                                    <form action="/admin/transaksi/{{ $item->id }}/tolak" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold w-100" title="Tolak" onclick="return confirm('Tolak peminjaman ini?')">
                                            <i class="bi bi-x-lg"></i> Tolak
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

                                <button type="button" class="btn btn-sm btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                    {{-- Modal Edit Form --}}
                                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="/admin/transaksi/{{ $item->id }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Transaksi #{{ $item->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            @foreach(['Menunggu Persetujuan','Sedang Dipinjam','Menunggu Pengembalian','Dikembalikan','Ditolak'] as $st)
                                                                <option value="{{ $st }}" {{ $item->status == $st ? 'selected' : '' }}>{{ $st }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deadline (YYYY-MM-DD)</label>
                                                        <input type="date" name="deadline" class="form-control" value="{{ $item->deadline }}">
                                                        <small class="text-muted">Ubah hanya jika perlu perpanjangan waktu.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

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
                <div class="d-flex justify-content-end mt-3">
                    {{ $transaksi->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection