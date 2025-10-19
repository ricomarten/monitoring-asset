<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BRI App Activity Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('head')
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #333;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: #6c757d;
            padding: 10px 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s;
        }

        .nav-link.active,
        .nav-link:hover {
            background-color: #0d6efd;
            color: white;
            border-radius: 25px;
        }

        .nav-link i {
            margin-right: 8px;
        }

        .pagination li a, .pagination li span {
            font-size: 12px;
            padding: 4px 10px;
        }

        .sidebar {
            background-color: #fff;
            min-height: 100vh;
            border-right: 1px solid #e0e0e0;
        }

        .table td, .table th {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">📱 BRI Activity Tracking</a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 180px;">
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ Request::is('dashboard/app-usage') ? 'active' : '' }}" href="{{ route('dashboard.app-usage') }}">
                            <i class="fas fa-object-group"></i> App Usage
                            <i class="fas fa-angle-right ms-auto"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('dashboard/call-usage') ? 'active' : '' }}" href="{{ route('dashboard.call-usage') }}">
                            <i class="fas fa-phone"></i> Call Logs
                            <i class="fas fa-angle-right ms-auto"></i>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    @yield('scripts')
</body>
</html>
