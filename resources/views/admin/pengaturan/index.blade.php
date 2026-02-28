@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold mt-2 mb-4">Pengaturan Sistem</h3>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 col-md-6">
        <div class="card-body">
            <form action="/admin/pengaturan" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Tarif Denda Keterlambatan (Per Hari)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" name="denda_per_hari" value="{{ $pengaturan->denda_per_hari }}" required>
                    </div>
                    <small class="text-muted">Nominal ini akan digunakan untuk menghitung denda buku yang terlambat.</small>
                </div>
                <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection