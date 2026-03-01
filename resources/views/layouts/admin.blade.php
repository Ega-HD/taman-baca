<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Taman Baca Assyfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark shadow">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 text-white min-vh-100">
                    <a href="/admin/dashboard" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 d-none d-sm-inline fw-bold">Admin Panel</span>
                    </a>
                    <hr class="w-100 bg-light">
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100" id="menu">
                        <li class="nav-item w-100 mb-2">
                            <a href="/admin/dashboard" class="nav-link px-0 align-middle text-white {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                                <i class="fs-4 bi-speedometer2"></i> <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item w-100 mb-2">
                            <a href="/admin/buku" class="nav-link px-0 align-middle text-white {{ Request::is('admin/buku*') ? 'active bg-secondary' : '' }}">
                                <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline">Kelola Buku</span>
                            </a>
                        </li>
                        <li class="nav-item w-100 mb-2">
                            <a href="/admin/transaksi" class="nav-link px-0 align-middle text-white {{ Request::is('admin/transaksi*') ? 'active bg-secondary' : '' }}">
                                <i class="fs-4 bi-arrow-left-right"></i> <span class="ms-1 d-none d-sm-inline">Transaksi</span>
                            </a>
                        </li>
                        <li class="nav-item w-100 mb-2">
                            <a href="/admin/pengaturan" class="nav-link px-0 align-middle text-white {{ Request::is('admin/pengaturan*') ? 'active bg-secondary' : '' }}">
                                <i class="fs-4 bi-box2-heart"></i> <span class="ms-1 d-none d-sm-inline">Tarif Denda</span>
                            </a>
                        </li>
                        <li class="nav-item w-100 mb-2">
                            <a href="/admin/members" class="nav-link px-0 align-middle text-white {{ Request::is('admin/members*') ? 'active bg-secondary' : '' }}">
                                <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Member</span>
                            </a>
                        </li>
                    </ul>
                    <div>
                        <a href="/" class="text-white text-decoration-none">
                            <i class="fs-4 bi-box-arrow-left"></i> <span class="d-none d-sm-inline mx-1">Kembali ke Web</span>
                        </a>
                    </div>
                    <hr class="w-100 bg-light">
                    <div class="pb-4">
                        
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill">
                                <i class="bi bi-power"></i> <span class="d-none d-sm-inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col py-4 bg-light">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>