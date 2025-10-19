@extends('layouts.app')

@section('title', 'App Usage Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Info Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Time Used (ms)</h6>
                        <h4 class="fw-bold">{{ number_format($totalTimeMs) }}</h4>
                    </div>
                    <div class="text-primary fs-2"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Devices</h6>
                        <h4 class="fw-bold">{{ $totalDevices }}</h4>
                    </div>
                    <div class="text-success fs-2"><i class="fas fa-mobile-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Visitors (Android Versions)</h6>
                        <h4 class="fw-bold">{{ $totalVisitors }}</h4>
                    </div>
                    <div class="text-warning fs-2"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5>📊 App Usage Logs</h5>
            <table id="appUsageTable" class="table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Device ID</th>
                        <th>App Name</th>
                        <th>Package</th>
                        <th>Rank</th>
                        <th>Category</th>
                        <th>Last Used</th>
                        <th>Total Time (H:M)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appUsageLogs as $log)
                        @foreach ($log['top_apps'] as $app)
                            <tr>
                                <td>{{ $log['device_id'] }}</td>
                                <td>{{ $app['app_name'] }}</td>
                                <td>{{ $app['package_name'] }}</td>
                                <td>{{ $app['rank'] }}</td>
                                <td>{{ $app['category'] }}</td>
                                <td>{{ $app['last_time_used'] }}</td>
                                <td>{{ gmdate("H:i", $app['total_time_ms'] / 1000) }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    $('#appUsageTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search: "Search:",
            info: "Showing _START_–_END_ of _TOTAL_ entries"
        }
    });
});
</script>
@endsection
