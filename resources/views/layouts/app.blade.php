<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    @yield('head') <!-- Allow custom styles/scripts in child views -->
</head>
<style>
    /* Mengatur jenis font dan ukuran font global */
    body {
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
        color: #333;
    }

    /* Teks dalam card, tabel, dan elemen lainnya akan mengikuti ukuran font default */
    .card, .table, .navbar{
        font-family: 'Roboto', sans-serif;
        font-size: 14px;
    }

    /* Menambahkan spasi tambahan di bawah setiap header untuk jarak */
    h1, h2, h3, h4, h5, h6 {
        margin-bottom: 10px;
    }

    /* Mengurangi ukuran font di dalam tabel untuk tampilan yang lebih ringkas */
    .table td, .table th {
        font-size: 13px;
    }

    /* Kelas untuk tombol navigasi */
    .nav-link {
        color: #6c757d; /* Teks berwarna abu-abu */
        padding: 12px 20px;
        border-radius: 10px; /* Sudut melengkung */
        transition: background-color 0.3s, color 0.3s;
        text-decoration: none; /* Menghilangkan garis bawah pada link */
    }

    /* Ikon panah di sebelah kanan */
    .nav-link .fa-arrow-right {
        font-size: 16px; /* Ukuran ikon panah */
        transition: transform 0.3s;
    }

    /* Efek hover pada nav-link */
    .nav-link:hover {
        background-color: #6f42c1; /* Warna biru keunguan saat hover */
        color: white; /* Mengubah warna teks menjadi putih saat hover */
        border-radius: 25px; /* Menambahkan efek rounded saat hover */
    }

    /* Mengubah posisi ikon panah saat hover */
    .nav-link:hover .fa-arrow-right {
        transform: translateX(5px); /* Pindahkan ikon panah sedikit ke kanan */
    }

    /* Kelas untuk link yang aktif */
    .nav-link.active {
        background-color: #6f42c1; /* Warna biru keunguan saat aktif */
        color: white; /* Mengubah warna teks menjadi putih saat aktif */
        border-radius: 25px; /* Menambahkan efek rounded saat aktif */
    }

    /* Spasi antara ikon dan teks */
    .nav-link i {
        margin-right: 10px;
    }
    /* Mengubah ukuran font pagination di Bootstrap */
    .pagination li a, .pagination li span {
        font-size: 12px; /* Ukuran font lebih kecil */
        padding: 5px 10px; /* Menyesuaikan padding agar tampilan tetap rapi */
    }

    .pagination li {
        margin-right: 5px; /* Memberikan jarak antara item pagination */
    }

</style>


<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">BRI App Activity Tracking</a>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4 col-lg-2 p-3 " style="background-color: #ffffff;">
                <!-- <h4 class="mb-4">Dashboard</h4> -->
                <!-- Logo -->
                <div class="mb-4 text-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid"
                        style="max-width: 200px;">
                </div>
                <ul class="nav flex-column">
                    <!-- Settings -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ Request::is('app-usage*') ? 'active' : '' }}" href="{{ route('app-usage.index') }}">
                            <i class="fas fa-object-group me-2"></i> App Activity Tracking
                            <i class="fas fa-angle-right ms-auto"></i>
                        </a>
                    </li>
                    <!-- History Call -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ Request::is('call-logs*') ? 'active' : '' }}" href="{{ route('call-logs.index') }}">
                            <i class="fas fa-circle-user me-2"></i>
                            History Call
                            <i class="fas fa-angle-right ms-auto"></i>
                        </a>
                    </li>

                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-8 col-lg-10 p-4" style="background-color: #f5f5f5;">
                @yield('content')
            </div>
        </div>


        <!-- jQuery (must be loaded before DataTables) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
        <!-- Bootstrap JS -->
        <!-- Bootstrap JS and Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

        @yield('scripts') <!-- Allow custom scripts in child views -->
</body>

</html>
