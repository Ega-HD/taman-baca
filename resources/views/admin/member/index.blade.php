@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
        <h3 class="fw-bold">Kelola Data Member</h3>
        <a href="/admin/members/create" class="btn btn-primary fw-bold"><i class="bi bi-person-plus"></i> Tambah Member</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama & Username</th>
                            <th>Kontak (WA)</th>
                            <th>Status Peminjaman</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold">{{ $member->nama_lengkap }}</span><br>
                                <small class="text-muted">@ {{ $member->username }}</small>
                            </td>
                            <td>{{ $member->no_hp }}</td>
                            <td>
                                @if($member->sedang_dipinjam > 0)
                                    <span class="badge bg-warning text-dark">Sedang Pinjam {{ $member->sedang_dipinjam }} Buku</span>
                                @else
                                    <span class="badge bg-success">Tidak Ada Pinjaman</span>
                                @endif
                            </td>
                            <td>{{ $member->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="/admin/members/{{ $member->id }}/edit" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                
                                <form action="/admin/members/{{ $member->id }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus member ini? Semua riwayat peminjaman (yang sudah kembali) juga akan terhapus.')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data member.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection