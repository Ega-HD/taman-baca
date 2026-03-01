<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taman Baca - PAUD Terpadu Assyfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">Taman Baca Assyfa</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Katalog Buku</a>
                    </li>
                    
                    @guest
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light btn-sm px-3 ms-lg-2 mt-2 mt-lg-0" href="/login">Login</a>
                        </li>
                    @else

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                    style="width: 35px; height: 35px; font-size: 0.9rem; font-weight: bold;">
                                    {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                                </div>
                                
                                <span class="fw-bold">{{ Auth::user()->nama_lengkap }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" aria-labelledby="navbarDropdown" style="min-width: 250px;">
                                
                                <div class="px-4 py-3 text-center bg-light border-bottom mb-2">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" 
                                        style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">
                                        {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                                    </div>
                                    
                                    <h6 class="fw-bold text-dark mb-0">{{ Auth::user()->nama_lengkap }}</h6>
                                    <small class="text-muted">@ {{ Auth::user()->username }}</small><br>
                                    
                                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')
                                        <span class="badge bg-danger mt-2">Administrator</span>
                                    @else
                                        <span class="badge bg-success mt-2">Member</span>
                                    @endif
                                </div>

                                @if(Auth::user()->role == 'member')
                                    <a class="dropdown-item py-2" href="/member/profile">
                                        <i class="bi bi-person-gear me-2"></i> Edit Profil Saya
                                    </a>
                                    <a class="dropdown-item py-2" href="/member/peminjaman">
                                        <i class="bi bi-book me-2"></i> Buku Saya
                                    </a>
                                @endif

                                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')
                                    <a class="dropdown-item py-2" href="/member/profile">
                                        <i class="bi bi-person-gear me-2"></i> Edit Profil Saya
                                    </a>
                                    <a class="dropdown-item py-2" href="/admin/dashboard">
                                        <i class="bi bi-speedometer2 me-2"></i> Dashboard Admin
                                    </a>
                                @endif

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item py-2 text-danger fw-bold" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>

                        {{-- <li class="nav-item dropdown mt-2 mt-lg-0">
                            <a class="nav-link dropdown-toggle fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Halo, {{ Auth::user()->nama_lengkap }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                
                                <li><a class="dropdown-item" href="/member/profile">Edit Profil Saya</a></li>
                                
                                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')
                                    <li><a class="dropdown-item" href="/admin/dashboard">Panel Admin</a></li>
                                @endif
                                
                                @if(Auth::user()->role == 'member')
                                    <li><a class="dropdown-item" href="/member/peminjaman">Buku Saya (Peminjaman)</a></li>
                                @endif
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="/logout" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li> --}}
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4 mb-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>