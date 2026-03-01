@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-2 mb-4">
        <h3 class="fw-bold">Detail Member</h3>
        <a href="/admin/members" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($member->nama_lengkap, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold">{{ $member->nama_lengkap }}</h5>
                    <p class="text-muted mb-1">@ {{ $member->username }}</p>
                    <span class="badge bg-info text-dark">Member</span>
                    
                    <hr>
                    
                    <div class="text-start">
                        <small class="text-muted d-block">No. HP (WA):</small>
                        <p class="fw-bold">{{ $member->no_hp }}</p>
                        
                        <small class="text-muted d-block">Bergabung Sejak:</small>
                        <p class="fw-bold">{{ $member->created_at->format('d M Y') }}</p>

                        <small class="text-muted d-block">Alamat:</small>
                        <p class="small">{{ $member->alamat ?? '-' }}</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="/admin/members/{{ $member->id }}/edit" class="btn btn-warning btn-sm fw-bold">Edit Profil</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history"></i> Riwayat Peminjaman Buku</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Buku</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Status</th>
                                    {{-- <th>Detail</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($member->transaksiPeminjaman as $item)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $item->itemBuku->buku->judul_buku }}</span><br>
                                        <small class="text-muted">{{ $item->itemBuku->kode_buku }}</small>
                                    </td>
                                    <td>
                                        @if($item->tgl_pinjam)
                                            {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($item->status) {
                                                'Sedang Dipinjam' => 'bg-primary',
                                                'Dikembalikan' => 'bg-success',
                                                'Ditolak' => 'bg-danger',
                                                'Menunggu Persetujuan' => 'bg-secondary',
                                                default => 'bg-warning text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $item->status }}</span>
                                    </td>
                                    {{-- <td>
                                        @if($item->total_denda > 0)
                                            <span class="text-danger small fw-bold">Denda: Rp {{ number_format($item->total_denda) }}</span>
                                        @elseif($item->status == 'Dikembalikan')
                                            <span class="text-success small"><i class="bi bi-check-all"></i> Selesai</span>
                                        @endif
                                    </td> --}}
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat peminjaman.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection