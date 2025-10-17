@extends('layouts.app')

@section('content')
    <style>
        /* Info Card Styles */
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

        /* Custom Styles for Table and Modal */
        .table-wrapper {
            margin-top: 30px;
        }
    </style>

    <div class="container">
        <!-- Info Card Section -->
        <div class="row justify-content-center"> <!-- Centering the card -->
            <div class="col-md-8 col-lg-8 mb-4">
                <div class="info-card">
                    <div class="icon-container">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="content">
                        <h5>Total Peer Number</h5>
                        <p>{{ $totalPeerNumber }}</p>
                    </div>
                    <div class="icon-container">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="content">
                        <h5>Total Call Duration</h5>
                        <p>{{ $totalCallDuration }}</p>
                    </div>
                </div>
            </div>
        </div>

        <br><br>
        <div class="card">
            <div class="card-body">
               <!-- Table Wrapper Section -->
            <div class="table-wrapper">
                <h4>History Call</h4>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search"></i> Find Specific Phone
                    </button>
                </div>

                <!-- Bootstrap Table -->
                <table id="callLogsTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Device Number</th>
                            <th>Peer Number</th>
                            <th>Call Type</th>
                            <th>Call Duration</th>
                            <th>Call Start</th>
                            <th>Call End</th>
                        </tr>
                    </thead>
                    <tbody id="callLogsTbody">
                        <!-- Data will be populated via JavaScript -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div id="pagination" class="pagination">
                    <!-- Pagination buttons will be generated here -->
                </div>

            </div>
            </div>

        </div>
    </div>

    <!-- Modal for Search and Filters -->
    <!-- Modal for Search and Filters -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search and Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter by Start Date -->
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate">
                    </div>

                    <!-- Filter by End Date -->
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate">
                    </div>

                    <!-- Search by Phone Number -->
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Search Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" placeholder="Enter phone number">
                    </div>

                    <!-- Sort By -->
                    <div class="mb-3">
                        <label for="sortBy" class="form-label">Sort By</label>
                        <select class="form-select" id="sortBy">
                            <option value="call_start">Call Start</option>
                            <option value="call_end">Call End</option>
                            <option value="call_duration">Call Duration</option>
                            <option value="device_number">Device Number</option>
                            <option value="peer_number">Peer Number</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="mb-3">
                        <label for="sortOrder" class="form-label">Sort Order</label>
                        <select class="form-select" id="sortOrder">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyFilters">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
    let currentPage = 1;

    // Inisialisasi DataTable dengan pengaturan server-side pagination
    $('#callLogsTable').DataTable({
        "processing": true,  // Menampilkan loader saat data sedang dimuat
        "serverSide": true,  // Menggunakan server-side pagination
        "ajax": {
            url: '/call-logs-with-calls',  // URL endpoint untuk mendapatkan data
            type: 'GET',
            data: function(d) {
                // Kirimkan parameter pencarian dan filter
                d.page = currentPage;  // Kirim halaman yang diminta
                d.length = d.length || 10;  // DataTable akan mengirimkan 'length' (jumlah per halaman)
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.phoneNumber = $('#phoneNumber').val();  // Pencarian berdasarkan nomor telepon
                d.sortBy = $('#sortBy').val();  // Kolom untuk sort
                d.sortOrder = $('#sortOrder').val();  // Ascending/Descending
            },
            dataSrc: function(response) {
                return response.callLogs;  // Data yang diterima dari server
            },
        },
        "columns": [
            { "data": "device_number" },
            { "data": "peer_number" },
            { "data": "call_type" },
            { "data": "call_duration" },
            { "data": "call_start" },
            { "data": "call_end" },
        ],
        "pagingType": "full_numbers",
        "language": {
            "lengthMenu": "Tampilkan _MENU_ entri per halaman",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ total entri)",
            "paginate": {
                "previous": "Sebelumnya",
                "next": "Berikutnya"
            }
        },
        "stateSave": true,  // Menyimpan status DataTable agar tetap konsisten antar reload
        "pageLength": 10,
        "searching": false,
        "drawCallback": function(settings) {
            currentPage = settings._iDisplayStart / settings._iDisplayLength + 1;
        }
    });

    // Aksi ketika tombol "Apply Filters" ditekan
    $('#applyFilters').click(function() {
        currentPage = 1; // Reset halaman ke 1 ketika filter diterapkan
        $('#callLogsTable').DataTable().ajax.reload();  // Reload DataTable dengan filter yang diterapkan
        $('#searchModal').modal('hide'); // Tutup modal setelah filter diterapkan
    });
});

    </script>
@endsection
