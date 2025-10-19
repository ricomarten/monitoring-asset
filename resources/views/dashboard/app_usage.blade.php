@extends('layouts.app')

@section('title', 'App Usage Dashboard')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 fw-bold text-primary">
        <i class="fas fa-chart-line me-2"></i>App Usage Dashboard
    </h4>

    <!-- ====== STATISTIC CARDS ====== -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card info-card shadow-sm border-0 p-3 d-flex flex-row align-items-center justify-content-between">
                <div class="icon-container bg-light p-3 rounded-circle">
                    <i class="fas fa-clock fa-2x text-primary"></i>
                </div>
                <div class="content text-end flex-grow-1 ms-3">
                    <h6 class="text-muted mb-1">Total Usage Time (ms)</h6>
                    <h4 class="fw-semibold text-dark mb-0">{{ number_format($totalTimeMs ?? 0) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card info-card shadow-sm border-0 p-3 d-flex flex-row align-items-center justify-content-between">
                <div class="icon-container bg-light p-3 rounded-circle">
                    <i class="fas fa-mobile-alt fa-2x text-success"></i>
                </div>
                <div class="content text-end flex-grow-1 ms-3">
                    <h6 class="text-muted mb-1">Total Devices</h6>
                    <h4 class="fw-semibold text-dark mb-0">{{ $totalDevices ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card info-card shadow-sm border-0 p-3 d-flex flex-row align-items-center justify-content-between">
                <div class="icon-container bg-light p-3 rounded-circle">
                    <i class="fas fa-users fa-2x text-info"></i>
                </div>
                <div class="content text-end flex-grow-1 ms-3">
                    <h6 class="text-muted mb-1">Total Visitors</h6>
                    <h4 class="fw-semibold text-dark mb-0">{{ $totalVisitors ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== CHART SECTION ====== -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-center mb-3 fw-semibold text-secondary">
                    <i class="fas fa-chart-pie me-2"></i>Top 5 Most Used Apps
                </h6>
                <div style="height:200px;">
                    <canvas id="topAppsPie" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-center mb-3 fw-semibold text-secondary">
                    <i class="fas fa-chart-line me-2"></i>App Usage Trend Over Time
                </h6>
                <div style="height:200px;">
                    <canvas id="usageLine" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ====== TABLE SECTION ====== -->
    <div class="card shadow-sm border-0 p-3">
        <h5 class="fw-bold text-primary mb-3">
            <i class="fas fa-table me-2"></i>App Usage Details
        </h5>
        <div class="table-responsive">
            <table id="appUsageTable" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Device ID</th>
                        <th>SIM Number</th>
                        <th>App Name</th>
                        <th>Package</th>
                        <th>Category</th>
                        <th>Rank</th>
                        <th>Last Used</th>
                        <th>Total Time (H:M)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appUsageLogs as $log)
                        @foreach ($log['top_apps'] as $app)
                            <tr>
                                <td>{{ $log['device_id'] }}</td>
                                <td>{{ $log['simNumber'] ?? '-' }}</td>
                                <td>{{ $app['app_name'] }}</td>
                                <td>{{ $app['package_name'] }}</td>
                                <td>{{ $app['category'] }}</td>
                                <td>{{ $app['rank'] }}</td>
                                <td>{{ $app['last_time_used'] }}</td>
                                <td>{{ gmdate('H:i', ($app['total_time_ms'] ?? 0) / 1000) }}</td>
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
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(function() {
    // DataTables Initialization
    $('#appUsageTable').DataTable({
        pagingType: 'full_numbers',
        pageLength: 10,
        responsive: true,
        language: {
            lengthMenu: 'Show _MENU_ entries per page',
            search: 'Search:',
            paginate: { previous: 'Prev', next: 'Next' }
        }
    });

    // Fetch and Render Chart Data
    $.getJSON("{{ route('app-usage.chart') }}", function(chartData) {
        if (!chartData.labels || !chartData.labels.length) return;

        // Pie Chart
        new Chart(document.getElementById('topAppsPie'), {
            type: 'pie',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.values,
                    backgroundColor: ['#6f42c1', '#00b894', '#0984e3', '#e17055', '#fdcb6e'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    title: { display: false }
                }
            }
        });

        // Line Chart
        const months = Object.keys(chartData.timeSeries || {});
        const datasets = [];
        const appNames = [...new Set(Object.values(chartData.timeSeries || {})
            .flatMap(month => Object.keys(month)))];

        appNames.forEach(app => {
            const values = months.map(m => chartData.timeSeries[m][app] || 0);
            datasets.push({
                label: app,
                data: values,
                fill: false,
                borderWidth: 2,
                tension: 0.3,
                borderColor: '#' + Math.floor(Math.random()*16777215).toString(16)
            });
        });

        new Chart(document.getElementById('usageLine'), {
            type: 'line',
            data: { labels: months, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
});
</script>
@endsection
