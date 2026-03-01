@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
        <h3 class="fw-bold">Kelola Data Akun</h3>
        <a href="/admin/members/create" class="btn btn-primary fw-bold"><i class="bi bi-person-plus"></i> Tambah Akun</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first('error') }}</div>
    @endif


    {{-- Awal Form Filter --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form action="/admin/members" method="GET">
                <div class="row g-2">

                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama, Username, No HP..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <select name="role" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Role</option>
                            @foreach($list_role as $r)
                                <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>
                                    {{ ucfirst($r) }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Data</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <a href="/admin/members" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- Awal Tabel --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama & Username</th>
                            <th>Role</th>
                            <th>Kontak (WA)</th>
                            <th>Status Peminjaman</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $index => $member)
                        <tr>
                            <td>{{ $members->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-bold">{{ $member->nama_lengkap }}</span><br>
                                <small class="text-muted">@ {{ $member->username }}</small>
                            </td>
                            <td>
                                @if($member->role == 'admin')
                                    <span class="badge bg-danger"><i class="bi bi-shield-lock"></i> Admin</span>
                                @else
                                    <span class="badge bg-success"><i class="bi bi-person"></i> Member</span>
                                @endif
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
                                <a href="/admin/members/{{ $member->id }}" class="btn btn-sm btn-info text-white" title="Lihat Detail & Riwayat">
                                    <i class="bi bi-eye"></i>
                                </a>

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
                <div class="d-flex justify-content-end mt-3">
                    {{ $members->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection