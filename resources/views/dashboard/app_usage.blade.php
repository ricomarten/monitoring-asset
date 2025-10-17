@extends('layouts.app')

@section('content')
    <style>
        .info-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .info-card .icon-container {
            width: 75px;
            height: 75px;
            background-color: #d4f7e4;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-card .icon-container i {
            font-size: 48px;
            color: #6f42c1;
        }

        .info-card .content {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .info-card h5 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .info-card p {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
            color: #333;
        }

        .table-wrapper {
            margin-top: 30px;
        }
    </style>

    <div class="container">
        <!-- Info Card Section -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-8 mb-4">
                <div class="info-card">
                    <div class="icon-container">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="content">
                        <h5>Total time used</h5>
                        <p>{{ $totalTimeMs }}</p>
                    </div>


                    <div class="icon-container">
                        <i class="fas fa-tablet-alt"></i>
                    </div>
                    <div class="content">
                        <h5>Total Device</h5>
                        <p>{{ $totalDevices }}</p>
                    </div>
                    <div class="icon-container">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="content">
                        <h5>Total Visitors</h5>
                        <p>{{ $totalVisitors }}</p>
                    </div>
                </div>


            </div>
        </div>

        <!-- Top Apps Section -->
        <div class="table-wrapper">
            <h4>Top Apps Usage</h4>
            <table id="appUsageTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Device ID</th>
                        <th>App Name</th>
                        <th>Phone Number</th>
                        <th>Last Time Used</th>
                        <th>Rank</th>
                        <th>Total Time Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appUsageLogs as $log)
                        @foreach ($log['top_apps'] as $topApp)
                            <tr>
                                <td>{{ $log['device_id'] }}</td>
                                <td>{{ $topApp['app_name'] }}</td>
                                <td>{{ $log['device_id'] }}</td> <!-- Or Phone number if available -->
                                <td>{{ $topApp['last_time_used'] }}</td>
                                <td>{{ $topApp['rank'] }}</td>
                                <td>{{ gmdate("H:i", $topApp['total_time_ms'] / 1000) }}</td> <!-- Total Time Duration in hours:minutes format -->
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#appUsageTable').DataTable({
                pagingType: 'full_numbers',  // Pagination buttons
                responsive: true,            // Responsive table
                dom: 'lfrtip',               // Display search box, length menu, table, info, and pagination
                language: {
                    lengthMenu: 'Show _MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    search: 'Search:',
                    paginate: {
                        previous: 'Previous',
                        next: 'Next'
                    }
                }
            });
        });
    </script>
@endsection
