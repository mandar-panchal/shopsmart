@extends('layouts.contentLayoutMaster')
@section('title', 'General Tasks')

@section('vendor-style')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<section id="general-tasks">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">General Tasks</h4>
                    @can('general_task')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                        <i class="bi bi-plus-lg me-1"></i> Create General Task
                    </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tasksTable">
                            <thead>
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Task Name</th>
                                    <th>Description</th>
                                    <th>Deadline</th>
                                    @can('general_task') <th>Created By</th>@endcan
                                    @can('general_task')<th>Created Date</th>@endcan
                                    <th>Assigned Executive Name</th>
                                    <th>Assigned Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($tasks as $key => $task)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $task->task_name }}</td>
                                    <td>{{ $task->task_description }}</td>
                                    @can('general_task')<td>{{ \Carbon\Carbon::parse($task->task_deadline)->format('d-m-Y') }}</td>
                                    <td>{{ $task->createdByUser->name ?? 'Unknown' }}</td>@endcan
                                    <td>{{ $task->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        {{ implode(', ', $task->assigned_users) ?: 'Not Assigned' }}
                                    </td>
                                    <td>{{ $task->assigned_date ? \Carbon\Carbon::parse($task->assigned_date)->format('d-m-Y') : '-' }}</td>
                                    <td>
                                        <button 
                                            class="btn btn-sm status-button" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#statusModal" 
                                            data-task-id="{{ $task->task_id }}" 
                                            data-task-status="{{ $task->status }}">
                                            {{ ucfirst($task->status) }}
                                        </button>
                                    </td>
                                    <td>
                                    @if(empty($task->assigned_to))
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-task-id="{{ $task->task_id }}">
                                        Delete Task
                                    </button>
                                    <button 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#assignTaskModal" 
                                    data-task-id="{{ $task->task_id }}"
                                    data-task-name="{{ $task->task_name }}"
                                    data-current-users="{{ $task->assigned_to }}"
                                    class="btn btn-primary">
                                    Assign Task
                                </button>
                                @else
                                    <div class="btn-group">
                                    @can('general_task')
                                        <button type="button" class="btn btn-warning btn-sm reassign-btn" 
                                                data-reassign-task-id="{{ $task->task_id }}"
                                                data-reassign-task-name="{{ $task->task_name }}"
                                                data-current-executive="{{ $task->assigned_to }}"
                                                data-current-executive-name="{{ optional($task->assignedUser)->name }}">
                                            Reassign
                                        </button>
                                    @endcan
                                    </div>
                                    @if(!$task->uploaded_date)
                                        @canany(['executive-tasks', 'general_task'])
                                            @php
                                                $assignedIds = explode(',', $task->assigned_to);
                                            @endphp
                                            @if(in_array(auth()->user()->id, $assignedIds)) 
                                            <button type="button" 
                                                class="btn btn-primary btn-sm upload-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#uploadModal"
                                                data-task-id="{{ $task->task_id }}"
                                                data-task-name="{{ $task->task_name }}">
                                                Upload
                                            </button>
                                           @endif
                                        @endcanany
                                        @endif
                                        @if($task->uploaded_date)
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                        class="btn btn-success btn-sm view-doc-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewDocModal"
                                                        data-task-id="{{ $task->task_id }}"
                                                        data-file-path="{{ $task->file_path }}"
                                                        data-task-name="{{ $task->task_name }}"
                                                        data-upload-date="{{ \Carbon\Carbon::parse($task->uploaded_date)->format('d-m-Y') }}">
                                                    View Document
                                                </button>
                                                @endif 
                                                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('executive') || $task->assigned_to == Auth::id())
                                                <button type="button"
                                                        class="btn btn-info btn-sm log-time-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#logTimeModal"
                                                        data-task-id="{{ $task->task_id }}"
                                                        data-task-name="{{ $task->task_name }}"
                                                        data-user-id="{{ $task->assigned_to }}">
                                                    Log Time
                                                </button>
                                                <button type="button"
                                                        class="btn btn-warning btn-sm edit-doc-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editDocModal"
                                                        data-task-id="{{ $task->task_id }}"
                                                        data-task-name="{{ $task->task_name }}"
                                                        data-upload-date="{{ \Carbon\Carbon::parse($task->uploaded_date)->format('Y-m-d') }}">
                                                    Edit
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                 @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for Status Update -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Task Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="task_id">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-select">
                            <option value="in_process">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add the Time Log Modal at the end of the section -->
<div class="modal fade" id="logTimeModal" tabindex="-1" aria-labelledby="logTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logTimeModalLabel">Log Time for Task: <span id="modal-task-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="logTimeForm">
                    @csrf
                    <input type="hidden" id="log_task_id" name="task_id">
                     <div class="mb-1">
                        <label for="time_spent" class="form-label">Time Spent (Hours:Minutes)</label>
                        <div class="d-flex">
                            <input type="number" class="form-control" id="time_hours" name="time_hours" min="0" step="1" placeholder="Hours" required>
                            <span class="mx-2">:</span>
                            <input type="number" class="form-control" id="time_minutes" name="time_minutes" min="0" step="1" max="59" placeholder="Minutes" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="reported_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="reported_date" name="reported_date" required>
                    </div>
                    <div class="mb-1">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" required></textarea>
                    </div>
                </form>
                <!-- Time Log History Table -->
                <div class="mt-4">
                    <h6>Previous Time Logs</h6>
                    <div class="table-responsive">
                        <table class="table table-sm" id="timeLogHistory">
                            <thead>
                                <tr>
                                <th>user</th>
                                    <th>Date</th>
                                    <th>Time Spent</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitTimeLog">Submit</button>
            </div>
        </div>
    </div>
</div>
 <!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="task_id" name="task_id" value="">
                <div class="modal-body">
                    <div id="error-messages" class="alert alert-danger d-none"></div>
                    <div class="mb-1">
                        <label for="document" class="form-label">Select Document</label>
                        <input type="file" class="form-control" id="document" name="document" required>
                    </div>
                    <div class="mb-1">
                        <label for="upload_date" class="form-label">Upload Date</label>
                        <input type="date" class="form-control" id="upload_date" name="upload_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- View Document Modal -->
    <div class="modal fade" id="viewDocModal" tabindex="-1" aria-labelledby="viewDocModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDocModalLabel">View Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-1">
                        <strong>Task Name:</strong> <span id="view-task-name"></span>
                    </div>
                    <div class="mb-1">
                        <strong>Upload Date:</strong> <span id="view-upload-date"></span>
                    </div>
                    <div class="mb-1">
                        <div id="document-viewer"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary" id="download-doc" target="_blank">Download Document</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Task Modal -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTaskModalLabel">Create General Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createTaskForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1">
                            <label for="task_name" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="task_name" name="task_name" required>
                        </div>
                        <div class="mb-1">
                            <label for="task_description" class="form-label">Task Description</label>
                            <textarea class="form-control" id="task_description" name="task_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-1">
                            <label for="task_deadline" class="form-label">Task Deadline</label>
                            <input type="date" class="form-control" id="task_deadline" name="task_deadline" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Add Edit Document Modal -->
<div class="modal fade" id="editDocModal" tabindex="-1" aria-labelledby="editDocModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocModalLabel">Edit Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDocForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_task_id" name="task_id">
                <div class="modal-body">
                    <div class="mb-1">
                        <label for="edit_document" class="form-label">Update Document (Optional)</label>
                        <input type="file" class="form-control" id="edit_document" name="document">
                    </div>
                    <div class="mb-1">
                        <label for="edit_upload_date" class="form-label">Upload Date</label>
                        <input type="date" class="form-control" id="edit_upload_date" name="upload_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Updated Modal with Multiple Select -->
<div class="modal fade" id="assignTaskModal" tabindex="-1" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignTaskModalLabel">Assign Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignTaskForm">
                @csrf
                <input type="hidden" id="assign_task_id" name="task_id">
                <div class="modal-body">
                    <div class="mb-1">
                        <label for="assigned_to" class="form-label">Select Users</label>
                        <select class="form-select select2-multiple" id="assigned_to" name="assigned_to[]" multiple required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">You can select multiple users by clicking or searching</small>
                    </div>
                    <div class="mb-1">
                        <label for="assigned_date" class="form-label">Assign Date</label>
                        <input type="date" class="form-control" id="assigned_date" name="assigned_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

</section>
@endsection

@section('vendor-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function () {
    // Initialize DataTable
    $('#tasksTable').DataTable({
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search tasks...',
        },
        pageLength: 10,
        "columnDefs": [
        {
            "targets": 0,
            "render": function (data, type, row, meta) {
                return meta.row + 1;
            }
        }
    ]
    });

    // Handle Create Task Form Submission
    $('#createTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: "{{ route('general-tasks.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#createTaskModal').modal('hide');
                $('#createTaskForm')[0].reset();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Task   has been created successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong while creating the task',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

// Store task details globally to persist through modal lifecycle
let currentTaskDetails = {
    taskId: null,
    taskName: null,
    currentExecutive: null,
    currentExecutiveName: null
};

// Handle Reassign Button Click
$(document).on('click', '.reassign-btn', function() {
    // Store the task details
    currentTaskDetails = {
        taskId: $(this).attr('data-reassign-task-id'),
        taskName: $(this).attr('data-reassign-task-name'),
        currentExecutive: $(this).attr('data-current-executive'),
        currentExecutiveName: $(this).attr('data-current-executive-name')
    };
    
    console.log('Button clicked - Task Details:', currentTaskDetails);

    // First confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to reassign task "${currentTaskDetails.taskName}" currently assigned to ${currentTaskDetails.currentExecutiveName}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed',
    }).then((result) => {
        if (result.isConfirmed) {
            // Second confirmation
            Swal.fire({
                title: 'Final Confirmation',
                text: 'Are you absolutely sure you want to reassign this task?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reassign it!',
            }).then((result2) => {
                if (result2.isConfirmed) {
                    const assignModal = new bootstrap.Modal(document.getElementById('assignTaskModal'));
                    assignModal.show();
                }
            });
        }
    });
});

// Handle modal show event
$('#assignTaskModal').on('show.bs.modal', function (event) {
    console.log('Modal show event triggered with currentTaskDetails:', currentTaskDetails);
    
    if (currentTaskDetails.taskId) {
        // Set the values when modal is being shown
        $('#assign_task_id').val(currentTaskDetails.taskId);
        $('#assignTaskModalLabel').text('Reassign Task: ' + currentTaskDetails.taskName);
        
        // Set current date
        const today = new Date().toISOString().split('T')[0];
        $('#assigned_date').val(today);
        
        // Update executive dropdown
        const $executiveSelect = $('#executive_id');
        $executiveSelect.find('option').show();
        
        if (currentTaskDetails.currentExecutive) {
            $executiveSelect.find(`option[value="${currentTaskDetails.currentExecutive}"]`).hide();
        }
        
        // Log the values right after setting
        console.log('Values set in modal:', {
            'assign_task_id': $('#assign_task_id').val(),
            'modal_label': $('#assignTaskModalLabel').text()
        });
    }
});

// Handle modal shown event
$('#assignTaskModal').on('shown.bs.modal', function (event) {
    // Verify values after modal is fully shown
    console.log('Modal fully shown - Verifying values:', {
        'assign_task_id': $('#assign_task_id').val(),
        'modal_label': $('#assignTaskModalLabel').text()
    });
});

// Handle modal hidden event - clean up
$('#assignTaskModal').on('hidden.bs.modal', function (event) {
    currentTaskDetails = {
        taskId: null,
        taskName: null,
        currentExecutive: null,
        currentExecutiveName: null
    };
});

// Also update your assign task form submission to use the stored task details
$('#assignTaskForm').on('submit', function(e) {
    e.preventDefault();
    
    if (!currentTaskDetails.taskId) {
        console.error('No task ID found for submission');
        return;
    }
    
    const formData = new FormData(this);
    formData.set('task_id', currentTaskDetails.taskId); // Ensure correct task ID is sent
    
    $.ajax({
        url: "{{ route('general-tasks.assign') }}", // Make sure this route is defined
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#assignTaskModal').modal('hide');
            $('#assignTaskForm')[0].reset();
            
            Swal.fire({
                title: 'Success!',
                text: `Task successfully reassigned`,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                location.reload();
            });
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: 'Something went wrong while reassigning the task',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
});

// Handle View Document Modal Show
$('#viewDocModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var taskName = button.data('task-name');
        var uploadDate = button.data('upload-date');
        var filePath = button.data('file-path');

        $('#view-task-name').text(taskName);
        $('#view-upload-date').text(uploadDate);
        $('#download-doc').attr('href', '/download-document/' + button.data('task-id'));

        // Handle document preview based on file type
        var fileExt = filePath.split('.').pop().toLowerCase();
        var viewerDiv = $('#document-viewer');
        viewerDiv.empty();

        if (['pdf'].includes(fileExt)) {
            viewerDiv.html(`<iframe src="/view-document/${button.data('task-id')}" width="100%" height="500px"></iframe>`);
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
            viewerDiv.html(`<img src="/view-document/${button.data('task-id')}" class="img-fluid" />`);
        } else {
            viewerDiv.html('<p class="text-muted">Preview not available for this file type. Please use the download button.</p>');
        }
    });
    $('#uploadModal').on('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        var button = $(event.relatedTarget);
        
        // Extract task data from button attributes
        var taskId = button.data('task-id');
        var taskName = button.data('task-name');
        
        // Store taskId in a data attribute on the modal itself
        $(this).data('task-id', taskId);
        
        // Set the hidden input value
        $(this).find('#task_id').val(taskId);
        
        // Set current date as default for upload_date
        var today = new Date().toISOString().split('T')[0];
        $('#upload_date').val(today);
        
        // Debug log
        console.log('Modal opened - Task ID:', taskId);
        console.log('Hidden input value:', $(this).find('#task_id').val());
    });

    // Handle Upload Form Submission
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get the task ID from the modal
        var taskId = $('#uploadModal').data('task-id');
        
        // Create new FormData object
        var formData = new FormData(this);
        
        // Ensure task_id is in the formData
        formData.set('task_id', taskId);
        
        // Debug log
        console.log('Submitting form with task_id:', taskId);
        
        // Validate task_id before submission
        if (!taskId) {
            Swal.fire({
                title: 'Error!',
                text: 'Task ID is missing. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        $.ajax({
            url: "{{ route('executive-tasks.upload') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#uploadModal').modal('hide');
                $('#uploadForm')[0].reset();
                
                Swal.fire({
                    title: 'Success!',
                    text: `Document uploaded successfully for task "${response.task_name}"`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    location.reload();
                });
            },
            error: function(xhr) {
                var errorMessage = 'Something went wrong while uploading the document';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.task_id) {
                    errorMessage = xhr.responseJSON.errors.task_id[0];
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
    
// Handle Edit Document Modal Show
$('#editDocModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var taskId = button.data('task-id');
    var taskName = button.data('task-name');
    var uploadDate = button.data('upload-date');
    
    $('#edit_task_id').val(taskId);
    $('#edit_upload_date').val(uploadDate);
    $('#editDocModalLabel').text('Edit Document - ' + taskName);
});

// Handle Edit Document Form Submission
$('#editDocForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var taskName = $('#editDocModalLabel').text().replace('Edit Document - ', '');
    
    $.ajax({
        url: "{{ route('executive-tasks.update-document') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#editDocModal').modal('hide');
            $('#editDocForm')[0].reset();
            
            Swal.fire({
                title: 'Success!',
                text: `Document successfully updated for task "${response.task_name}"`,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                location.reload();
            });
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: 'Something went wrong while updating the document',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
});
$('.select2-multiple').select2({
        dropdownParent: $('#assignTaskModal'),
        width: '100%',
        placeholder: "Select users",
        allowClear: true,
        multiple: true
    });


$('#assignTaskModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var taskId = button.data('task-id');
    var taskName = button.data('task-name');
    var currentUsers = button.data('current-users'); // This should be a comma-separated string

    $('#assign_task_id').val(taskId);
    $('#assignTaskModalLabel').text('Assign Task: ' + taskName);

    // Set current date as default
    var today = new Date().toISOString().split('T')[0];
    $('#assigned_date').val(today);

    // Reset and update user selection
    $('#assigned_to').val(null).trigger('change');
    if (currentUsers) {
        var userIds = currentUsers.split(',');
        $('#assigned_to').val(userIds).trigger('change');
    }
});

$('#assignTaskForm').on('submit', function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitButton = $(this).find('button[type="submit"]');
    submitButton.prop('disabled', true);
    
    $.ajax({
        url: "{{ route('general-tasks.assign') }}",
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                // Hide modal first
                $('#assignTaskModal').modal('hide');
                
                // Reset form
                $('#assignTaskForm')[0].reset();
                $('#assigned_to').val(null).trigger('change');
                
                // Construct user names text
                var userNamesText = response.user_names.join(', ');
                var actionText = response.is_reassign ? 'reassigned' : 'assigned';
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: `Users "${userNamesText}" have been ${actionText} to task "${response.task_name}" successfully`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to assign task',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr) {
            let errorMessage = 'Something went wrong while assigning the task';
            
            // Check if there's a validation error message
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
                title: 'Error!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },
        complete: function() {
            // Re-enable submit button
            submitButton.prop('disabled', false);
        }
    });
});
// Handle Log Time Modal Show
$('#logTimeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var taskId = button.data('task-id');
    var taskName = button.data('task-name');
    
    // Set task details in modal
    $('#modal-task-name').text(taskName);
    $('#log_task_id').val(taskId);
    
    // Set current date as default
    var today = new Date().toISOString().split('T')[0];
    $('#reported_date').val(today);
    
    // Clear previous form data
    $('#time_hours').val('');
    $('#time_minutes').val('');
    $('#notes').val('');
    
    // Load previous time logs
    loadTimeLogs(taskId);
});

// Load Time Logs
function loadTimeLogs(taskId) {
    $.ajax({
        url: "{{ route('executive-tasks.time-logs', '') }}/" + taskId,
        method: 'GET',
        data: {
            user_id: $('#log_task_id').data('user-id') // This will pass the assigned user's ID
        },
        success: function(response) {
            var tbody = $('#timeLogHistory tbody');
            tbody.empty();
            
            response.logs.forEach(function(log) {
                tbody.append(`
                    <tr>
                    <td>
                        <span class="avatar">
                            <img class="round" src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('images/portrait/small/avatar-s-11.jpg') }}" alt="avatar" height="30" width="30">
                            <span class="avatar-status-online" 
                                  data-user-name="${log.user_name}" 
                                  data-task-id="${log.task_id}"></span>
                        </span>
                    </td>
                    <td>${formatDate(log.reported_date)}</td>
                    <td>${log.time_spent}</td>
                    <td>${log.notes || 'no notes'}</td>
                    </tr>
                `);
            });

            // Add click event for green dot (avatar)
            $('.avatar-status-online').on('click', function() {
                var userName = $(this).data('user-name');
                alert('User: ' + userName); // Display user's name in an alert or modal
            });
        },
        error: function(xhr) {
            console.error('Error loading time logs:', xhr);
        }
    });
}

// Handle Time Log Submission
$('#submitTimeLog').on('click', function() {
    var formData = new FormData($('#logTimeForm')[0]);
    
    $.ajax({
        url: "{{ route('executive-tasks.log-time') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#logTimeModal').modal('hide');
            
            Swal.fire({
                title: 'Success!',
                text: 'Time logged successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Optionally reload the page or update the UI
                location.reload();
            });
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to log time. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
});

    // Set the CSRF token for all AJAX requests
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

    $(document).on('click', '.delete-btn', function () {
    var taskId = $(this).data('task-id');  // Get Task ID from the button's data attribute
    
    // First confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "This task will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            // Second confirmation after the user confirms the first one
            Swal.fire({
                title: 'Are you really sure?',
                text: "This action cannot be undone!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, really delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send an AJAX request to delete the task
                    $.ajax({
                        url: '/tasks/delete/' + taskId,
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                // Remove the row from the table if the deletion was successful
                                $('tr[data-task-id="' + taskId + '"]').remove();
                                Swal.fire('Deleted!', 'Your task has been deleted.', 'success');
                            } else {
                                Swal.fire('Error!', 'There was an issue deleting the task.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'There was an error with the deletion.', 'error');
                        }
                    });
                }
            });
        }
    });
});
$(document).on('click', '.avatar-status-online', function () {
    const taskId = $(this).data('task-id'); // Assume this attribute holds the task ID

    $.ajax({
        url: `/executive-tasks/time-logs/${taskId}`,
        method: 'GET',
        success: function(response) {
            if (response.success && response.logs.length > 0) {
                // Extract unique user names from the logs
                const userNames = [...new Set(response.logs.map(log => log.user_name))];
                
                if (userNames.length > 0) {
                    // Show user names in a SweetAlert popup
                    Swal.fire({
                        title: 'Reported By',
                        html: userNames.join('<br>'), // Display user names in a line-break separated list
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // If no users are found
                    Swal.fire({
                        title: 'No Reports',
                        text: 'No users have reported time for this task.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                // If there are no logs or unsuccessful response
                Swal.fire({
                    title: 'No Reports',
                    text: 'No users have reported time for this task.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            // Handle error in AJAX request
            console.error("Error fetching time logs:", error);
            Swal.fire({
                title: 'Error',
                text: 'Unable to fetch time logs. Please try again later.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
});// Open the modal with the current task status
$(document).ready(function () {
    // Handle click on status button
    $('.status-button').click(function (e) {
        var status = $(this).data('task-status');

        // Prevent modal from opening if status is 'completed'
        if (status === 'completed') {
            e.preventDefault();  // Prevent default action of button
            Swal.fire({
                icon: 'info',
                title: 'Already Completed',
                text: 'This task is already completed.'
            });
        }
    });

    // Open the modal with the current task status
    $('#statusModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var taskId = button.data('task-id');
        var taskStatus = button.data('task-status');
        
        var modal = $(this);
        modal.find('#task_id').val(taskId);
        modal.find('#status').val(taskStatus);
    });

    // Handle status update
    $('#statusForm').submit(function (e) {
        e.preventDefault();
        
        var taskId = $('#task_id').val();
        var status = $('#status').val();

        $.ajax({
            url: '/tasks/' + taskId + '/update-status',
            method: 'POST',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: 'Task status has been updated successfully.',
                    }).then(() => {
                        $('#statusModal').modal('hide');
                        location.reload(); 
                    });
                }
            }
        });
    });
});
</script>
@endsection