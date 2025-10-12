@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">History Call</h2>

    <table id="callLogsTable" class="table table-striped table-bordered">
        <thead class="table-dark">

            <tr>
                <th>Device Number</th>
                <th>Peer Number</th>
                <th>Call Type</th>
                <th>Call Duration</th>
                <th>Call Start</th>
                <th>Call End</th>
            </tr>
        </thead>
        <tbody>
            @foreach($callLogs as $log)
            <tr>
                <td>{{ $log['device_number'] }}</td>
                <td>{{ $log['peer_number'] }}</td>
                <td>{{ $log['call_type'] }}</td>
                <td>{{ $log['call_duration'] }}</td>
                <td>{{ $log['call_start'] }}</td>
                <td>{{ $log['call_end'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
