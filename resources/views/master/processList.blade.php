@extends('layouts.contentLayoutMaster')

@section('title', 'Process List')

@section('vendor-style')

<!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <style>
        /* Enhanced Styling */
        .nav-tabs .nav-link {
            transition: all 0.3s ease;
            color: #495057;
            font-weight: 600;
        }
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table thead {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .dataTables_wrapper .dataTables_filter {
            text-align: right;
            margin-bottom: 10px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.375rem 0.75rem;
            width: 250px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .stage-divider {
            background-color: #e9ecef !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-upload {
            display: flex;
            align-items: center;
            gap: 5px;
        }
                /* Make sure the modal content is responsive */
        #fileContent {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Responsive iframe for PDFs, DOCs, PPTs */
        #fileContent iframe {
            width: 100%;
            height: 500px;
            border: none;
        }

        /* Responsive video for media content */
        #fileContent video {
            width: 100%;
            height: auto;
            max-height: 80vh; /* Ensures the video does not stretch beyond the viewport height */
        }

        /* Ensure modal content adapts to window size */
        .modal-dialog.modal-lg {
            max-width: 90%; /* Adjust modal width to 90% of the screen width */
        }

        .modal-body {
            overflow: hidden;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
<section id="process-list">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fas fa-list-alt me-2"></i>Process List for {{ $projects->society_name }}
            </h4>
            <div class="d-flex align-items-center">
                <span class="me-2 text-white">Current Stage:</span>
                <span class="badge bg-light text-dark">{{ $currentStage ?? 'Not Started' }}</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-2 text-white">Search:</span>
                <input type="text" id="globalSearch" class="form-control form-control-sm" style="width: 250px;" placeholder="Search across tabs">
            </div>
        </div>
        <div class="card-body">
            <!-- Nav tabs with icons -->
            <ul class="nav nav-tabs nav-fill" id="processListTabs" role="tablist">
            @can('pmc_application')
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pmc-tab" data-bs-toggle="tab" data-bs-target="#pmc-application" type="button" role="tab">
                        <i class="fas fa-file-alt me-2"></i>PMC Application
                    </button>
                </li>
                @endcan
                @can('stage1')
                <li class="nav-item" role="presentation">
                    <button class="nav-link disabled" id="stage1-tab" data-bs-toggle="tab" data-bs-target="#stage1" type="button" role="tab">
                        <i class="fas fa-cube me-2"></i>Stage 1
                    </button>
                </li>
                @endcan
                @can('stage2')
                <li class="nav-item" role="presentation">
                    <button class="nav-link disabled" id="stage2-tab" data-bs-toggle="tab" data-bs-target="#stage2" type="button" role="tab">
                        <i class="fas fa-cubes me-2"></i>Stage 2
                    </button>
                </li>
                @endcan
            </ul>

<!-- Tab content -->
<div class="tab-content mt-3" id="processListTabContent">
        <!-- PMC Application Tab -->
    @can('pmc_application')
        <div class="tab-pane fade show active" id="pmc-application" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover" id="pmcApplicationTable">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Process Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="stage-divider">
                            <td colspan="4" class="text-center">PMC APPLICATION</td>
                        </tr>
                    @foreach ($pmcApplicationProcesses as $key => $process)
                        <tr id="process-{{ $process->process_id }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $process->process_name }}</td>
                            <td>
                                @php
                                        // Find the matching task for the current process
                                        $task = $tasks->firstWhere('process_id', $process->process_id);
                                @endphp
                                @if ($task)
                                    <span class="badge {{ $task->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($task->status ?? 'Pending') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Task</span>
                                @endif
                            </td>
                            <td>
                                @if ($task && $task->file_path)
                                <a href="/storage/{{ $task->file_path }}" class="btn btn-sm btn-primary btn-view-document">
                                     <i class="fas fa-file"></i> View File
                                </a>
                                <button class="btn btn-sm btn-warning btn-edit-document" 
                                        data-task-id="{{ $task->id }}"
                                        data-project-id="{{ $projects->id }}"
                                        data-process-id="{{ $process->process_id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                    <button class="btn btn-sm btn-secondary send-mail-btn" 
                                        data-task-id="{{ $task->id }}"
                                        data-project-id="{{ $projects->id }}">
                                    <i class="fas fa-envelope"></i> Send Mail
                                </button>
                                @else
                                    <button class="btn btn-sm btn-info btn-upload"
                                            data-project-id="{{ $projects->id }}"
                                            data-process-id="{{ $process->process_id }}">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                @endif
                            </td>                           
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                    </div>
                    <div class="footer mt-4 text-center">
                    <button id="startProjectButton" class="btn btn-primary">Start Project</button>
                    </div>
                </div>
                @endcan
                <!-- Stage 1 Tab -->
                @php
                $isAdmin = Auth::user()->hasRole('Admin'); // Check if the user is an admin
            @endphp

<!-- Stage 1 Tab -->
@can('stage1')
<div class="tab-pane fade" id="stage1" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-striped" id="stage1Table">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Process Name</th>
                        @can('view_process')
                            <th>Assign To</th>
                            <th>Assigned Name</th>
                        @endcan
                    <th>Status</th>
                    <th>Deadline</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sections = [
                        11 => 'ASSOCIATION TO SOCIETY',
                        19 => 'FEASIBILITY REPORT',
                        22 => 'TENDER DRAFT',
                        25 => 'WEBSITE DATA',
                        26 => 'TENDER PROCESS',
                        64 => 'SOCIETY MEMBER\'S CONSENT FORMS',
                        65 => '79 A ORDER'
                    ];
                @endphp
                
                <!-- Only show processes assigned to the executive if user is an executive -->
                @php
                    $displayProcesses = auth()->user()->hasRole('executive') 
                        ? $stage1Processes->filter(function($process) {
                            return $process->task && $process->task->assigned_to == auth()->id();
                        })
                        : $stage1Processes;
                @endphp

                <tr>
                    <td colspan="{{ auth()->user()->hasRole('executive') ? '4' : '6' }}" class="text-center bg-light" style="font-weight: bold;">
                        SOCIETY / ASSOCIATION DOCUMENTATION
                    </td>
                </tr>

                @foreach ($displayProcesses as $key => $process)
                    @if (array_key_exists($key + 1, $sections))
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('executive') ? '4' : '6' }}" class="text-center" style="font-weight: bold;">
                                {{ $sections[$key + 1] }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $process->process_name }}</td>

                        @can('view_process')<td>
                        @if ($process->task && $process->task->is_assigned)
                            <!-- If assigned, show "Assigned" and disable the button -->
                            <button class="btn btn-sm btn-secondary" disabled>
                                Assigned
                            </button>
                        @else
                            <!-- If not assigned, show the "Assign To" button -->
                            <button class="btn btn-sm btn-primary btn-assign"
                                    data-toggle="modal"
                                    data-target="#assignModal"
                                    data-process-id="{{ $process->process_id }}"
                                    data-project-id="{{ $projects->id }}"
                                    data-process-name="{{ $process->process_name }}"
                                    id="assignBtn-{{ $process->process_id }}">
                                Assign To
                            </button>
                        @endif
                    </td>
                   
                    <td>
                        @if ($process->task && $process->task->assignedUser)
                            <span class="text-success">{{ $process->task->assignedUser->name }}</span>
                        @else
                            Not Assigned
                        @endif
                    </td>

                    @endcan
                    <td>
                                @php
                                        // Find the matching task for the current process
                                        $task = $tasks->firstWhere('process_id', $process->process_id);
                                       

                                @endphp
                                @if ($task)
                                    <span class="badge {{ $task->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($task->status ?? 'Pending') }}
                                    </span>
                                
                                    @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($process->deadline == 'Completed')
                                    <span class="text-success">{{ $process->deadline }}</span>
                                @elseif($process->deadline == 'Late Completed')
                                    <span class="text-warning">{{ $process->deadline }} <i class="fas fa-exclamation-triangle"></i></span>
                                @elseif($process->deadline == 'Overdue')
                                    <span class="text-danger">{{ $process->deadline }}</span>
                                @elseif($process->deadline == 'Due today')
                                    <span class="text-warning">{{ $process->deadline }}</span>
                                @elseif($process->deadline == 'Not assigned' || $process->deadline == 'Day not defined')
                                    <span class="text-secondary">{{ $process->deadline }}</span>
                                @else
                                    <span class="text-danger">{{ $process->deadline }}</span>
                                @endif
                            </td>
                        
                            <td>
                            
                            @if ($task && $task->file_path)
                                    <!-- If the file is uploaded, show the "View File" button -->
                                    <a href="/storage/{{ $task->file_path }}" class="btn btn-sm btn-primary btn-view-document">
                                        <i class="fas fa-file"></i> View File
                                    </a>
                                    <button class="btn btn-sm btn-secondary send-mail-btn" 
                                            data-task-id="{{ $task->id ?? '' }}" 
                                            data-project-id="{{ $projects->id ?? '' }}"> <!-- Handle null $projects -->
                                        <i class="fas fa-envelope"></i> Send Mail
                                    </button>
                            @else
                                    @can('view_process')
                                        <!-- If no file is uploaded, show "Document is not uploaded" -->
                                        <span class="text-danger">Not yet uploaded</span>
                            @endif
                                    @endcan
                                
                                    @if ($task) <!-- Check if $task is not null -->
                                        <button class="btn btn-sm btn-warning btn-edit-document" 
                                                data-task-id="{{ $task->id }}"
                                                data-project-id="{{ $projects->id ?? '' }}" 
                                                data-process-id="{{ $process->process_id ?? '' }}"> <!-- Use null coalescing for $process -->
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    @endif
                                

                                    <!-- <button class="btn btn-sm btn-secondary send-mail-btn" 
                                            data-task-id="{{ $task->id ?? '' }}" 
                                            data-project-id="{{ $projects->id ?? '' }}">  Handle null $projects -->
                                        <!-- <i class="fas fa-envelope"></i> Send Mail
                                    </button> --> 

                                    @if (!$task || !$task->file_path)
                                        @can('executive_acess')
                                            <button class="btn btn-sm btn-info btn-upload"
                                                    data-project-id="{{ $projects->id ?? '' }}" 
                                                    data-process-id="{{ $process->process_id ?? '' }}"> <!-- Handle null $process -->
                                                <i class="fas fa-upload"></i> Upload
                                            </button>
                                        @endcan
                                    @endif
                            </td>
                    </tr>
                @endforeach
            </tbody>            
        </table>
    </div>
</div>
@endcan
<!-- Email Modal -->
<div class="modal fade" id="sendMailModal" tabindex="-1" aria-labelledby="sendMailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendMailModalLabel">Send Mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendMailForm">
                    <input type="hidden" id="taskId" name="task_id">
                    <input type="hidden" id="projectId" name="project_id">
                    <div class="mb-3">
                        <label for="recipientType" class="form-label">Select Recipient Type</label>
                        <select class="form-select" id="recipientType" name="recipient_type" required>
                            <option value="">Choose...</option>
                            <option value="executive">Executive</option>
                            <option value="society">Society</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>

                    <div id="emailList" class="mb-3">
                        <!-- Email checkboxes will be populated here -->
                    </div>

                    <div class="mb-3">
                        <div class="uploaded-doc">
                            <strong>Uploaded Document:</strong>
                            <span id="documentName"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendEmailBtn">Send</button>
            </div>
        </div>
    </div>
</div>


<!-- View File Modal -->
<div class="modal fade" id="viewFileModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFileModalLabel">View File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="fileContent">
                <!-- File content will be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Add this modal for editing document -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentModalLabel">Edit Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDocumentForm" enctype="multipart/form-data">
                    <input type="hidden" id="editTaskId" name="task_id">
                    <input type="hidden" id="editProjectId" name="project_id">
                    <input type="hidden" id="editProcessId" name="process_id">
                    
                    <div class="mb-3">
                        <label for="editDocument" class="form-label">Upload New Document</label>
                        <input type="file" class="form-control" id="editDocument" name="document">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editUploadedDate" class="form-label">Upload Date</label>
                        <input type="date" class="form-control datepicker" id="editUploadedDate" name="uploaded_date">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Assign Process</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    <input type="hidden" id="processId" name="process_id">
                    <div class="mb-3">
                        <label for="executive" class="form-label">Select Executive</label>
                        <select id="executive" name="executive_id" class="form-select">
                            @foreach ($executives as $executive)
                                <option value="{{ $executive->id }}">{{ $executive->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignDate" class="form-label">Assign Date</label>
                        <input type="date" id="assignDate" name="assign_date" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="assignButton">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Stage 2 Tab -->
@can('stage2')
<div class="tab-pane fade" id="stage2" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-hover" id="stage2Table">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Process Name</th>
                    @if(!auth()->user()->hasRole('executive'))
                        @can('view_process')
                            <th>Assign To</th>
                            <th>Assigned Name</th>
                        @endcan
                    @endif
                    <th>Status</th>
                    <th>Deadline</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sections = [
                        12 => 'LEASE DEED',
                        25 => 'CARPET AREA CONFIRMATION',
                        29 => 'SITE SURVEY',
                        35 => 'DESIGN COORDINATION',
                        40 => 'DEVELOPMENT AGREEMENT',
                        50 => 'NOC\'S',
                        64 => 'PROJECT SCHEDULE',
                        68 => 'DRAWINGS FROM CONSULTANTS',
                        74 => 'APPROVALS',
                        79 => 'PAAA',
                        85 => 'TRANSIT ACCOMMODATION'
                    ];

                    // Filter processes for executives
                    $displayProcesses = auth()->user()->hasRole('executive') 
                        ? $stage2Processes->filter(function($process) {
                            return $process->task && $process->task->assigned_to == auth()->id();
                        })
                        : $stage2Processes;
                @endphp

                <tr>
                    <td colspan="{{ auth()->user()->hasRole('executive') ? '4' : '6' }}" class="text-center" style="font-weight: bold;">
                        DELAPIDATED CERTIFICATE
                    </td>
                </tr>
                @foreach ($displayProcesses as $key => $process)
                    @if (array_key_exists($key + 1, $sections))
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('executive') ? '4' : '6' }}" class="text-center" style="font-weight: bold; background-color: #f8f9fa;">
                                {{ $sections[$key + 1] }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $process->process_name }}</td>
                    @can('view_process')
                        <td>
                        @if ($process->task && $process->task->is_assigned)
                            <!-- If assigned, show "Assigned" and disable the button -->
                            <button class="btn btn-sm btn-secondary" disabled>
                                Assigned
                            </button>
                        @else
                            <!-- If not assigned, show the "Assign To" button -->
                            <button class="btn btn-sm btn-primary btn-assign"
                                    data-toggle="modal"
                                    data-target="#assignModal"
                                    data-process-id="{{ $process->process_id }}"
                                    data-project-id="{{ $projects->id }}"
                                    data-process-name="{{ $process->process_name }}"
                                    id="assignBtn-{{ $process->process_id }}">
                                Assign To
                            </button>
                        @endif
                        </td>
                        <td>
                        @if ($process->task && $process->task->assignedUser)
                            <span class="text-success">{{ $process->task->assignedUser->name }}</span>
                        @else
                            Not Assigned
                        @endif
                        </td>
                    @endcan
                    
                    <td>
                         @php
                            // Find the matching task for the current process
                            $task = $tasks->firstWhere('process_id', $process->process_id);
                            // Get the assigned date and add the days from the process (e.g., 45 days)
                            $assignedDate = $task ? \Carbon\Carbon::parse($task->assigned_date) : null;
                            $deadline = $assignedDate ? $assignedDate->addDays($process->day) : null;
                            // Calculate remaining days if deadline is set
                            $remainingDays = $deadline ? $deadline->diffInDays(now()) : null;
                        @endphp
                            @if ($task)
                                <span class="badge {{ $task->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($task->status ?? 'Pending') }}
                                </span>
                                
                            @else
                                <span class="badge bg-danger">Pending</span>
                            @endif
                    </td>
                    <td>
                        @if ($remainingDays !== null)
                            @if ($remainingDays > 0)
                                <span class="text-warning">{{ $remainingDays }} days remaining</span>
                            @elseif ($remainingDays == 0)
                                <span class="text-danger">Deadline today</span>
                            @else
                                <span class="text-danger">{{ abs($remainingDays) }} days overdue</span>
                            @endif
                        @else
                            <span class="text-muted">No deadline set</span>
                        @endif
                    </td>
                    <td>
                        @if ($task && $task->file_path)
                            <!-- If the file is uploaded, show the "View File" button -->
                            <a href="/storage/{{ $task->file_path }}" class="btn btn-sm btn-primary btn-view-document">
                                <i class="fas fa-file"></i> View File
                            </a>
                            <button class="btn btn-sm btn-secondary send-mail-btn" 
                                    data-task-id="{{ $task->id ?? '' }}" 
                                    data-project-id="{{ $projects->id ?? '' }}"> <!-- Handle null $projects -->
                                <i class="fas fa-envelope"></i> Send Mail
                            </button>
                        @else
                            @can('view_process')
                                <!-- If no file is uploaded, show "Document is not uploaded" -->
                                <span class="text-danger">Not yet uploaded</span>
                        @endif
                            @endcan
                        
                            @if ($task) <!-- Check if $task is not null -->
                                <button class="btn btn-sm btn-warning btn-edit-document" 
                                        data-task-id="{{ $task->id }}"
                                        data-project-id="{{ $projects->id ?? '' }}" 
                                        data-process-id="{{ $process->process_id ?? '' }}"> <!-- Use null coalescing for $process -->
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            @endif
                        <!-- <button class="btn btn-sm btn-secondary send-mail-btn" 
                        data-task-id="{{ $task->id ?? '' }}" 
                        data-project-id="{{ $projects->id ?? '' }}">  Handle null $projects -->
                        <!-- <i class="fas fa-envelope"></i> Send Mail
                        </button> --> 
                        @if (!$task || !$task->file_path)
                                @can('executive_acess')
                                    <button class="btn btn-sm btn-info btn-upload"
                                            data-project-id="{{ $projects->id ?? '' }}" 
                                            data-process-id="{{ $process->process_id ?? '' }}"> <!-- Handle null $process -->
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                @endcan
                         @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endcan
 
 <!-- Document Upload Modal -->
            <div class="modal fade" id="documentUploadModal" tabindex="-1" aria-labelledby="documentUploadModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="documentUploadModalLabel">Upload Document</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="documentUploadForm" action="{{ route('task.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="project_id" id="uploadProjectId" value="{{ $projects->id }}">
                                <input type="hidden" name="process_id" id="uploadProcessId">
                                <input type="hidden" name="process_name" id="uploadProcessName"> 
                                
                                <div class="mb-3">
                                    <label for="documentFile" class="form-label">Select Document</label>
                                    <input type="file" class="form-control" id="documentFile" name="document" required accept=".jpg,.jpeg,.png,.gif,.ppt,.pdf,.pptx,.doc,.docx,.mp4">
                                    <!-- <input type="file" class="form-control" id="documentFile" name="document" required> -->
                                </div>
                                
                                <div class="mb-3">
                                    <label for="uploadDate" class="form-label">Upload Date</label>
                                    <input type="date" class="form-control" id="uploadDate" name="uploaded_date"required >
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">submit </button>
                            </div>
                        </form>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function () {
    const tableOptions = {
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: '',
            searchPlaceholder: 'Search this table...',
        },
        processing: true
    };

    // Initialize DataTables
    const pmcTable = $('#pmcApplicationTable').DataTable(tableOptions);
    const stage1Table = $('#stage1Table').DataTable(tableOptions);
    const stage2Table = $('#stage2Table').DataTable(tableOptions);
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd', // Format as year-month-day
        autoclose: true,      // Close the calendar after selecting a date
        todayHighlight: true, // Highlight today's date
        defaultViewDate: { year: new Date().getFullYear(), month: new Date().getMonth(), day: new Date().getDate() },
    }).datepicker('setDate', new Date());
    // Global search across all tables
    $('#globalSearch').on('keyup', function () {
        const searchTerm = $(this).val();
        
        pmcTable.search(searchTerm).draw();
        stage1Table.search(searchTerm).draw();
        stage2Table.search(searchTerm).draw();
    });

    // Adjust tables when switching tabs
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const tabId = $(e.target).attr('id');
    localStorage.setItem('lastActiveTab', tabId);
});
});
// Function to check upload status and handle button state
function checkUploadStatus(tasks) {
    tasks.forEach(task => {
        const { process_id, project_id, buttonSelector } = task;

        $.ajax({
            url: '/check-upload-status', // Your route
            type: 'GET',
            data: { 
                process_id: process_id, 
                project_id: project_id 
            },
            success: function(response) {
                const uploadButton = $(buttonSelector);

                if (response.file_path) {
                    // Update button state
                    uploadButton
                        .prop('disabled', true)
                        .removeClass('btn-info')
                        .addClass('btn-success')
                        .html('<i class="fas fa-check"></i> Uploaded');

                    // Add view document button that opens in new tab
                    if (!uploadButton.next('.btn-view-document').length) {
                        uploadButton.after(`
                            <a href="${response.file_path}" 
                               class="btn btn-sm btn-primary ms-2 btn-view-document" 
                               target="_blank"
                               title="View Document">
                                <i class="fas fa-file-alt"></i> View Document
                            </a>
                        `);
                    }

                    // Store file path in session storage
                    sessionStorage.setItem(`document-${process_id}`, response.file_path);
                }
            },
            error: function(xhr) {
                console.error('Error checking upload status:', xhr.responseText);
            }
        });
    });
}
// Document upload handler
$('.btn-upload').on('click', function(e) {
    const uploadButton = $(this);
    const processId = uploadButton.data('process-id');
    const processName = uploadButton.data('process-name');
    const projectId = {{ $projects->id }};  // Ensure this is available from the backend (e.g., Blade syntax).

    $('#documentUploadForm')[0].reset(); // Reset the form
    $('.datepicker').datepicker('clearDates');

    // Check upload status before opening modal
    $.ajax({
        url: '{{ route("check.upload.status") }}',  // Make sure this route exists in your Laravel backend
        type: 'GET',
        data: { 
            process_id: processId, 
            project_id: projectId 
        },
        success: function(response) {
            if (response.file_path) {
                // Document already uploaded
                Swal.fire({
                    title: 'Document Already Uploaded',
                    html: `
                        <p>Process: ${processName}</p>
                        <a href="${response.file_path}" 
                            target="_blank" 
                            class="btn btn-sm btn-success ms-2" 
                            title="View Uploaded Document">
                                <i class="fas fa-file-alt"></i> View Document
                        </a>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            } else {
                // Proceed with upload
                $('#uploadProcessId').val(processId);
                $('#uploadProcessName').val(processName);
                $('#uploadProjectId').val(projectId);

                // Initialize the date picker
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });

                $('#documentUploadModal').modal('show');
            }
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Error',
                text: 'Unable to check upload status. Please try again.',
                icon: 'error'
            });
        }
    });
});

// Form submission handler
$('#documentUploadForm').on('submit', function(e) {
    e.preventDefault();

    const form = $(this);
    const processId = $('#uploadProcessId').val();
    const processName = $('#uploadProcessName').val();
    const uploadButton = $(`.btn-upload[data-process-id="${processId}"]`);

    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response) {
            $('#documentUploadModal').modal('hide');

            if (response.file_path) {
                // Store file path in sessionStorage
                sessionStorage.setItem(`document-${processId}`, response.file_path);

                // Update the UI for the uploaded button
                uploadButton
                    .prop('disabled', true)
                    .removeClass('btn-info')
                    .addClass('btn-success')
                    .html('<i class="fas fa-check"></i> Uploaded');

                // Add view document button if not already present
                if (!uploadButton.next('.btn-view-document').length) {
                    uploadButton.after(`
                        <a href="${response.file_path}" 
                           class="btn btn-sm btn-primary ms-2 btn-view-document" 
                           target="_blank"
                           title="View Document">
                            <i class="fas fa-file-alt"></i> View Document
                        </a>
                    `);
                }

                Swal.fire({
                    title: 'Upload Successful!',
                    icon: 'success',
                    confirmButtonText: 'Close'
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Upload Failed',
                text: xhr.responseJSON?.message || 'Unable to upload document. Please try again.',
                icon: 'error'
            });
        }
    });
});

// Function to open document upload modal
function openDocumentUploadModal(projectId, processId, processName) {
    document.getElementById('uploadProjectId').value = projectId;
    document.getElementById('uploadProcessId').value = processId;
    document.getElementById('uploadProcessName').value = processName;
    $('#documentUploadModal').modal('show');
}

// Add click event listener for the "View Document" link
// Add a click event listener for the "View Document" link
$(document).on('click', '.btn-view-document', function() {
    const processId = $(this).prev('.btn-upload').data('process-id'); // Get the processId from the corresponding button
    const documentUrl = sessionStorage.getItem(`document-${processId}`);

    if (documentUrl) {
        // Clear previous content in modal
        $('#fileContent').empty();

        // Extract the file extension
        const fileExtension = documentUrl.split('.').pop().toLowerCase();

        if (['mp4', 'avi', 'mov', 'wmv'].includes(fileExtension)) {
            // If the file is a video, load a video player inside the modal
            const videoHtml = `
                <video id="videoPlayer" controls>
                    <source src="${documentUrl}" type="video/${fileExtension}">
                    Your browser does not support the video tag.
                </video>
            `;
            $('#fileContent').html(videoHtml);

            // Show the modal with the video player
            $('#viewFileModal').modal('show');

            // Play the video
            $('#videoPlayer')[0].play();

            // Reset the video when the modal is closed
            $('#viewFileModal').on('hidden.bs.modal', function () {
                const videoElement = document.getElementById('videoPlayer');
                videoElement.pause();
                videoElement.currentTime = 0; // Reset video to start
            });
        } else if (['pdf', 'txt', 'doc', 'docx', 'rtf'].includes(fileExtension)) {
            // For PDF, Word Documents, and text files, embed them in an iframe
            const iframeHtml = `
                <iframe src="${documentUrl}" width="100%" height="100%" style="border:none;"></iframe>
            `;
            $('#fileContent').html(iframeHtml);

            // Show the modal with the document preview
            $('#viewFileModal').modal('show');
        } else if (['ppt', 'pptx'].includes(fileExtension)) {
            // If the file is a PowerPoint, use a PowerPoint viewer (via Google Docs Viewer)
            const pptHtml = `
                <iframe src="https://docs.google.com/gview?url=${documentUrl}&embedded=true" width="100%" height="500px" style="border:none;"></iframe>
            `;
            $('#fileContent').html(pptHtml);

            // Show the modal with the PowerPoint preview
            $('#viewFileModal').modal('show');
        } else {
            // For unsupported file types, just show a message
            Swal.fire({
                title: 'Unsupported File Type',
                text: 'This file type is not supported for preview.',
                icon: 'error'
            });
        }
    } 
});



function manageTabsAndProcesses(isProjectStarted) {
    if (isProjectStarted) {
        // Disable Stage 1 and Stage 2 tabs initially
        $('#stage1-tab, #stage2-tab').addClass('disabled');
        
        // Disable all upload buttons in PMC Application
        $('#pmcApplicationTable .btn-upload').prop('disabled', true)
            .removeClass('btn-info')
            .addClass('btn-secondary')
            .html('<i class="fas fa-lock"></i> Locked');
    }
}
function checkProjectStatus() {
    $.ajax({
        url: '{{ route("check.project.status") }}',
        method: 'GET',
        data: { 
            
            project_id: {{ $projects->id }} 
        },
        success: function(response) {
            if (response.isStarted) {
                // Permanently activate tabs
                $('#startProjectButton')
                    .prop('disabled', true)
                    .addClass('btn-secondary')
                    .removeClass('btn-primary')
                    .text('Project Started');

                // Remove disabled class from Stage 1 and Stage 2 tabs
                $('#stage1-tab, #stage2-tab')
                    .removeClass('disabled')
                    .prop('disabled', false);

                // Restore last active tab
                const lastActiveTab = localStorage.getItem('lastActiveTab') || 'stage1-tab';
                $(`#${lastActiveTab}`).tab('show');
            }
        },
        error: function() {
            console.error('Failed to check project status');
        }
    });
}

    // Call status check on page load
    checkProjectStatus();
    $('.nav-tabs .disabled').on('click', function(e) {
        e.preventDefault();
        return false;
    });

    // Start Project Button Click Handler
    $('#startProjectButton').on('click', function () {
    // First confirmation alert
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to start the project.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, start it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Second confirmation alert
            Swal.fire({
                title: 'Confirm Start',
                text: "Do you really want to start the project?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Start Project',
                cancelButtonText: 'Cancel'
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    // Show loading spinner
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while the project is starting.',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX call to update the status
                    $.ajax({
                        url: '{{ route("start.project") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            project_id: {{ $projects->id }}
                        },
                        success: function (response) {
                            // Close loading spinner
                            Swal.close();

                            // Success notification
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });

                            // Enable Stage 1 and Stage 2 tabs
                            $('#stage1-tab, #stage2-tab').removeClass('disabled');

                            // Switch to Stage 1 tab
                            var firstStageTab = new bootstrap.Tab(document.getElementById('stage1-tab'));
                            firstStageTab.show();

                            // Disable start button permanently
                            $('#startProjectButton')
                                .prop('disabled', true)
                                .addClass('btn-secondary')
                                .removeClass('btn-primary')
                                .text('Project Started');
                        },
                        error: function (xhr) {
                            // Close loading spinner
                            Swal.close();

                            // Error notification
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'An unexpected error occurred.',
                                icon: 'error',
                                confirmButtonText: 'Try Again'
                            });
                        }
                    });
                }
            });
        }
    });
});
// Initialize modal
const assignModal = new bootstrap.Modal(document.getElementById('assignModal'));

$(document).on('click', '.btn-assign', function () {
    const processId = $(this).data('process-id');
    const projectId = $(this).data('project-id');

    $('#processId').val(processId);
    $('#projectId').val(projectId);

    const processName = $(this).data('process-name');
    $('#assignModalLabel').text(`Assign Process: ${processName}`);
    
    // Open modal
    assignModal.show();
});

// Handle modal close button click
$(document).on('click', '[data-bs-dismiss="modal"]', function() {
    assignModal.hide();
    // Remove backdrop manually
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});

// Clear modal fields when closing
$('#assignModal').on('hidden.bs.modal', function () {
    $('#assignModalLabel').text('Assign Process');
    $('#assignForm')[0].reset();
    // Remove backdrop and body class
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});

$('#assignButton').on('click', function (e) {
    e.preventDefault();

    const formData = $('#assignForm').serialize();
    const projectId = '{{ $projects->id }}'; // Get the project ID from Blade

    // Append project_id to formData
    const data = formData + '&project_id=' + projectId;
    console.log(data);

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to assign this process?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, assign it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('assign.process') }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    console.log('Assignment response:', response);
                    Swal.fire({
                        title: 'Assigned!',
                        text: 'Process has been assigned successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        const processId = $('#processId').val();

                        const assignButton = $(`#assignBtn-${processId}`);
                        assignButton
                            .prop('disabled', true)
                            .text('Assigned')
                            .removeClass('btn-primary')
                            .addClass('btn-secondary');

                        // Close modal and clean up
                        assignModal.hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    });
                },
                error: function (xhr) {
                    console.error('AJAX error:', xhr);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred: ' + xhr.responseText,
                        icon: 'error',
                        confirmButtonText: 'Close'
                    });
                }
            });
        }
    });
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// $('.send-mail-btn').click(function() {
//     const taskId = $(this).data('task-id');
//     const projectId = $(this).data('project-id');
    
//     Swal.fire({
//         title: 'Send Document',
//         html: `
//             <input type="email" id="email" class="swal2-input" placeholder="Enter email address">
//         `,
//         showCancelButton: true,
//         confirmButtonText: 'Send',
//         showLoaderOnConfirm: true,
//         preConfirm: (email) => {
//             const emailValue = document.getElementById('email').value;
//             if (!emailValue) {
//                 Swal.showValidationMessage('Please enter an email address');
//                 return false;
//             }
            
//             return $.ajax({
//                 url: `/tasks/${taskId}/send-email`,
//                 type: 'POST',
//                 data: {
//                     email: emailValue,
//                     project_id: projectId
//                 },
//                 dataType: 'json'
//             })
//             .done(function(response) {
//                 if (response.status === 200) {
//                     return response;
//                 } else {
//                     throw new Error(response.error || 'Failed to send email');
//                 }
//             })
//             .fail(function(jqXHR) {
//                 throw new Error(jqXHR.responseJSON?.error || 'Failed to send email');
//             });
//         }
//     }).then((result) => {
//         if (result.isConfirmed) {
//             Swal.fire('Success', 'Email sent successfully', 'success');
//         }
//     }).catch(error => {
//         Swal.fire('Error', error.message, 'error');
//     });
// }); 
// // Handle View File button click
// First, ensure proper event delegation since content may be loaded dynamically
$(document).on('click', '.btn-view-document', function(e) {
    e.preventDefault();
    const fileUrl = $(this).attr('href');
    const fileContentDiv = $('#fileContent');
    
    // Clear existing content
    fileContentDiv.empty();
    
    // Show loading indicator
    fileContentDiv.html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    
    // Check if file URL exists
    if (!fileUrl) {
        fileContentDiv.html('<div class="alert alert-danger">Error: File URL not found</div>');
        $('#viewFileModal').modal('show');
        return;
    }
    
    // Function to handle errors
    const handleError = () => {
        fileContentDiv.html(`
            <div class="alert alert-danger">
                Error loading the file. Please try downloading it directly:
                <a href="${fileUrl}" target="_blank" class="btn btn-primary mt-2">Download File</a>
            </div>
        `);
    };
    
    // Check file type and load accordingly
    const fileExtension = fileUrl.split('.').pop().toLowerCase();
    
    try {
        switch (fileExtension) {
            case 'pdf':
                fileContentDiv.html(`
                    <div class="ratio ratio-16x9">
                        <iframe src="${fileUrl}" 
                                class="w-100" 
                                style="height: 75vh;" 
                                allowfullscreen>
                        </iframe>
                    </div>
                `);
                break;
                
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                const img = new Image();
                img.onload = function() {
                    fileContentDiv.html(`
                        <div class="text-center">
                            <img src="${fileUrl}" 
                                 class="img-fluid" 
                                 alt="Document Preview"
                                 style="max-height: 75vh;">
                        </div>
                    `);
                };
                img.onerror = handleError;
                img.src = fileUrl;
                break;
                
            default:
                fileContentDiv.html(`
                    <div class="alert alert-warning">
                        This file type cannot be previewed directly. Please download it instead:
                        <a href="${fileUrl}" target="_blank" class="btn btn-primary mt-2">Download File</a>
                    </div>
                `);
        }
    } catch (error) {
        handleError();
    }
    
    // Show the modal
    $('#viewFileModal').modal('show');
});

// Handle modal cleanup when closed
$('#viewFileModal').on('hidden.bs.modal', function () {
    $('#fileContent').empty();
});
$('.btn-edit-document').click(function() {
        const taskId = $(this).data('task-id');
        const projectId = $(this).data('project-id');
        const processId = $(this).data('process-id');

        $('#editTaskId').val(taskId);
        $('#editProjectId').val(projectId);
        $('#editProcessId').val(processId);

        $('#editDocumentModal').modal('show');
    });

    // Handle form submission
    $('.btn-edit-document').click(function() {
        const taskId = $(this).data('task-id');
        const projectId = $(this).data('project-id');
        const processId = $(this).data('process-id');

        $('#editTaskId').val(taskId);
        $('#editProjectId').val(projectId);
        $('#editProcessId').val(processId);

        $('#editDocumentModal').modal('show');
    });

    // Handle form submission
    $('#editDocumentForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: "{{ route('upload-document') }}", // Using Laravel's route helper
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editDocumentModal').modal('hide');
                
                // Show success message using SweetAlert
                Swal.fire({
                    title: 'Success!',
                    text: 'Document updated successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reload the page or update the table
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseJSON); // Log the error details
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update document: ' + (xhr.responseJSON?.message || error),
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
// Initialize modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('sendMailModal');
    
    // Handle send mail button click
    document.querySelectorAll('.send-mail-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const projectId = this.dataset.projectId;
            
            // Set hidden input values
            document.getElementById('taskId').value = taskId;
            document.getElementById('projectId').value = projectId;
            
            // Initialize modal with Bootstrap
            const mailModal = new bootstrap.Modal(modal);
            mailModal.show();
            
            // Fetch current task details
            fetchTaskDetails(taskId);
        });
    });
    
    // Handle recipient type change
    document.getElementById('recipientType').addEventListener('change', function() {
        const projectId = document.getElementById('projectId').value;
        if (this.value === 'executive') {
            fetchExecutives();
        } else if (this.value === 'society') {
            fetchSocietyEmails(projectId);
        }
    });
    
    // Handle send email button click
    document.getElementById('sendEmailBtn').addEventListener('click', sendEmail);
});

// Fetch task details
function fetchTaskDetails(taskId) {
    fetch(`/api/tasks/${taskId}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                // Display uploaded document name
                document.getElementById('documentName').textContent = data.file_path.split('/').pop();
                
                // Pre-select executive if task is assigned
                if (data.assigned_to) {
                    document.getElementById('recipientType').value = 'executive';
                    document.getElementById('recipientType').dispatchEvent(new Event('change'));
                }
            } else {
                throw new Error(result.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to fetch task details'
            });
        });
}

// Fetch executives list
function fetchExecutives() {
    fetch('/api/users/executives')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const emailList = document.getElementById('emailList');
                emailList.innerHTML = '';
                
                result.data.forEach(executive => {
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    div.innerHTML = `
                        <input class="form-check-input" type="checkbox" value="${executive.email}" 
                               id="exec${executive.id}" name="emails[]">
                        <label class="form-check-label" for="exec${executive.id}">
                            ${executive.name} (${executive.email})
                        </label>
                    `;
                    emailList.appendChild(div);
                });
            } else {
                throw new Error(result.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to fetch executives'
            });
        });
}

// Fetch society emails
function fetchSocietyEmails(projectId) {
    fetch(`/api/projects/${projectId}/contacts`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const emailList = document.getElementById('emailList');
                emailList.innerHTML = '';
                
                const emails = [
                    { label: 'President', email: result.data.president_email },
                    { label: 'Vice President', email: result.data.vice_president_email },
                    { label: 'Secretary', email: result.data.secretary_email }
                ];
                
                emails.forEach((contact, index) => {
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    div.innerHTML = `
                        <input class="form-check-input" type="checkbox" value="${contact.email}" 
                               id="contact${index}" name="emails[]">
                        <label class="form-check-label" for="contact${index}">
                            ${contact.label} (${contact.email})
                        </label>
                    `;
                    emailList.appendChild(div);
                });
            } else {
                throw new Error(result.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to fetch society contacts'
            });
        });
}

// Send email function
// Send email function
function sendEmail() {
    const form = document.getElementById('sendMailForm');
    const formData = new FormData();
    
    // Get task and project IDs
    formData.append('task_id', document.getElementById('taskId').value);
    formData.append('project_id', document.getElementById('projectId').value);
    
    // Get note
    formData.append('note', document.getElementById('note').value);
    
    // Get selected emails and append them properly
    const selectedEmails = Array.from(document.querySelectorAll('input[name="emails[]"]:checked'))
        .map(cb => cb.value);
    
    if (selectedEmails.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please select at least one recipient'
        });
        return;
    }
    
    // Append each email individually to create a proper array in PHP
    selectedEmails.forEach((email, index) => {
        formData.append(`emails[${index}]`, email);
    });
    
    fetch('/api/send-mail', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Email sent successfully!'
            }).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('sendMailModal')).hide();
            });
        } else {
            throw new Error(result.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to send email'
        });
    });
}

// Call on page load to check existing uploads
$(document).ready(function() {
    checkUploadStatus();
    checkUploadStatus();
});
</script>
@endsection