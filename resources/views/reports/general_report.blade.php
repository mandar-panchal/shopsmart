@extends('layouts.contentLayoutMaster')
@section('title', 'Time Report')

@section('vendor-style')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<section id="time-report">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Time Report</h4>
                </div>
                <div class="card-body">
                    <form id="reportForm" method="POST" action="{{ route('report.generate') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="daterange">Date Range</label>
                                    <input type="text" class="form-control" id="daterange" name="daterange" value="{{ old('daterange', request('daterange')) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_id">Select User (Optional)</label>
                                    <select class="form-control select2" id="user_id" name="user_id">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block">Generate Report</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4" id="reportTableContainer" style="display: none;">
                        <table class="table table-bordered" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Username</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('vendor-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Store the last selected date range
    let lastSelectedRange = localStorage.getItem('lastDateRange') || null;
    let startDate = moment().subtract(6, 'days');
    let endDate = moment();

    // If we have a last selected range, use it
    if (lastSelectedRange) {
        let [start, end] = lastSelectedRange.split(' - ');
        startDate = moment(start, 'DD/MM/YYYY');
        endDate = moment(end, 'DD/MM/YYYY');
    }

    // Initialize date range picker
    $('#daterange').daterangepicker({
        startDate: startDate,
        endDate: endDate,
        locale: {
            format: 'DD/MM/YYYY'
        }
    }, function(start, end) {
        // Save the selected range when it changes
        localStorage.setItem('lastDateRange', start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    });

    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select User (Optional)",
        allowClear: true
    });

    let reportTable;

    // Handle form submission
    $('#reportForm').on('submit', function(e) {
        e.preventDefault();
        
        // Save the current date range
        localStorage.setItem('lastDateRange', $('#daterange').val());
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                updateReportTable(response.data);
            },
            error: function(xhr) {
                console.error('Error generating report:', xhr);
                alert('Error generating report. Please try again.');
            }
        });
    });

    function updateReportTable(data) {
        // Destroy existing DataTable if it exists
        if (reportTable) {
            reportTable.destroy();
        }

        // Get the table element
        const table = $('#reportTable');
        
        // Clear existing headers and create new ones
        const headerRow = table.find('thead tr');
        headerRow.empty();
        headerRow.append('<th>Sr. No</th><th>Username</th>');
        
        // Add date columns
        data.dates.forEach(date => {
            headerRow.append(`<th>${date}</th>`);
        });

        // Clear and populate table body
        const tableBody = table.find('tbody');
        tableBody.empty();

        data.reportData.forEach((row, index) => {
            let tr = $('<tr>');
            tr.append(`<td>${index + 1}</td>`);
            tr.append(`<td>${row.username}</td>`);
            
            // Add hours for each date
            row.daily_hours.forEach(hours => {
                tr.append(`<td>${hours}</td>`);
            });

            tableBody.append(tr);
        });

        // Show the table container
        $('#reportTableContainer').show();

        // Initialize DataTable
        reportTable = table.DataTable({
            pageLength: 25,
            ordering: true,
            responsive: true
        });
    }

    // If we have stored date range and user_id values, trigger report generation
    if (lastSelectedRange) {
        $('#reportForm').trigger('submit');
    }
});
</script>
@endsection