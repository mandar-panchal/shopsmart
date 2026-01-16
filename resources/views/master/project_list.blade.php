@extends('layouts.contentLayoutMaster')
@section('title', 'Project List')
@section('vendor-style')
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<section id="project-list">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Project List</h4>
                    <a href="{{ route('project.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add New Project
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="projectsTable">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Society Name</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $key => $project)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $project->society_name }}</td>
                                        <td>{{ $project->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('project.processes', $project->id) }}" class="btn btn-sm btn-primary">
                                                View Processes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
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
    <!-- DataTables and Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function () {
    $('#projectsTable').DataTable({
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search projects...',
        },
        pageLength: 10,
    });
});
</script>
@endsection