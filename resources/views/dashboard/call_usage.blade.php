@extends('layouts.app')

@section('title', 'Call Usage Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Info Cards -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Peer Numbers</h6>
                        <h4 class="mb-0 fw-bold">{{ $totalPeerNumber }}</h4>
                    </div>
                    <div class="text-primary fs-2">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Call Duration (sec)</h6>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalCallDuration) }}</h4>
                    </div>
                    <div class="text-success fs-2">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>📞 Call Logs</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

            <table id="callLogsTable" class="table table-striped table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Device Number</th>
                        <th>Peer Number</th>
                        <th>Type</th>
                        <th>Duration (s)</th>
                        <th>Start</th>
                        <th>End</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="filterForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Call Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control mb-3" id="startDate">

                <label class="form-label">End Date</label>
                <input type="date" class="form-control mb-3" id="endDate">

                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control mb-3" id="phoneNumber" placeholder="Enter number">

                <label class="form-label">Sort Order</label>
                <select class="form-select" id="sortOrder">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    const table = $('#callLogsTable').DataTable({
        ajax: {
            url: '/api/call-logs-with-calls',
            data: function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.phoneNumber = $('#phoneNumber').val();
                d.sortOrder = $('#sortOrder').val();
            },
            dataSrc: 'callLogs'
        },
        columns: [
            { data: 'device_number' },
            { data: 'peer_number' },
            { data: 'call_type' },
            { data: 'call_duration' },
            { data: 'call_start' },
            { data: 'call_end' },
        ],
        responsive: true,
        pageLength: 10,
    });

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
        $('#filterModal').modal('hide');
    });
});
</script>
@endsection
