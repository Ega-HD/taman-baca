<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Member Baru - Taman Baca Assyfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .card-register { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    </style>
  </head>
  <body>
    
    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-md-6 col-lg-5">
            <div class="card card-register p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary"><i class="bi bi-book-half"></i> Daftar Member</h3>
                    <p class="text-muted">Bergabunglah dengan Taman Baca Assyfa</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/register" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Budi Santoso" value="{{ old('nama_lengkap') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">No. Handphone (WA)</label>
                        <input 
                            type="text" 
                            name="no_hp" 
                            class="form-control" 
                            placeholder="0812xxxx" 
                            value="{{ old('no_hp') }}" 
                            inputmode="numeric" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Buat username unik" value="{{ old('username') }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                            <small class="text-muted" style="font-size: 0.75rem">*Ulangi password</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mt-2">Daftar Sekarang</button>
                    
                    <div class="text-center mt-4">
                        <small class="text-muted">Sudah punya akun? <a href="/login" class="text-decoration-none fw-bold">Login disini</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>