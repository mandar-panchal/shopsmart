@extends('layouts/contentLayoutMaster')

@section('title', 'Jobs List')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="col-md-12 col-12">
  <div class="card">
    <div class="card-header border-bottom bg-primary d-flex justify-content-between align-items-center">
        <h4 class="card-title text-white">Jobs List</h4>
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('cs'))
        <button type="button" class="btn btn-light btn-sm" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> Create New Job
        </button>
        @endif
    </div>

    <div class="row p-2">
      <div class="col-md-3 mb-1">
        <label class="form-label">Filter by</label>
        <select class="form-control form-select" id="date-filter" onchange="showDateRange(this.value)">
          <option value="missed">Missed Deadlines</option>
          <option value="yesterday">Yesterday</option>
          <option value="today" selected>Today</option>
          <option value="tomorrow">Tomorrow</option>
          <option value="this_month">This Month</option>
          <option value="custom">Custom date range</option>
        </select>
      </div>

      <div class="col-md-9">
        <div class="row custom-date-range" style="display:none;">
          <div class="col-md-5">
            <label class="form-label">From date</label>
            <input type="date" id="from-date" class="form-control">
          </div>
          <div class="col-md-5">
            <label class="form-label">To date</label>
            <input type="date" id="to-date" class="form-control">
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary mt-4" onclick="refreshTable()">Apply</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
  <div class="table-responsive">
      <table class="datatables-jobs table table-striped">
        <thead>
          <tr>
            <th>Title</th>
            <th>Client</th>
            <th>Deadline</th>
            <th>Created By</th>
            <th>Planner</th>
            <th>Operator</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>


<!-- Add this modal structure at the bottom of index.blade.php before the scripts -->
<div class="modal fade" id="createJobModal" tabindex="-1" aria-labelledby="createJobModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="createJobModalLabel">Create New Job</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createJobForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="title">Job Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required />
                <div class="invalid-feedback" id="title-error"></div>
              </div>
            </div>

            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="client_name">Client Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="client_name" name="client_name" required />
                <div class="invalid-feedback" id="client_name-error"></div>
              </div>
            </div>

            <div class="col-12">
              <div class="mb-1">
                <label class="form-label" for="description">Job Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                <div class="invalid-feedback" id="description-error"></div>
              </div>
            </div>

            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="deadline">Deadline <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control flatpickr-date-time" id="deadline" name="deadline" required />
                <div class="invalid-feedback" id="deadline-error"></div>
              </div>
            </div>

            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label class="form-label" for="sample_file">Sample File <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="sample_file" name="sample_file" required />
                <div class="invalid-feedback" id="sample_file-error"></div>
              </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="mb-1">
                    <label class="form-label" for="planner_id">Assign to Planner <span class="text-danger">*</span></label>
                    <select class="form-control" id="planner_id" name="planner_id" required>
                        <option value="">Select Planner</option>
                        @foreach($planners as $planner)
                            <option value="{{ $planner->id }}">{{ $planner->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="planner_id-error"></div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="submitJob">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            Create Job
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('vendor-script')
  <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

@endsection

@section('page-script')
<script>
let jobTable;

$(function () {
  initializeDataTable();
});

function initializeDataTable() {
  jobTable = $('.datatables-jobs').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route("jobs.data") }}',
      data: function(d) {
        d.date_filter = $('#date-filter').val();
        d.from_date = $('#from-date').val();
        d.to_date = $('#to-date').val();
      }
    },
    columns: [
      { data: 'title' },
      { data: 'client_name' },
      { 
        data: 'deadline',
        render: function(data, type, row) {
          return moment(data).format('DD-MM-YYYY');
        }
      },
      { data: 'creator.name' },
      { data: 'planner.name' },
      { 
        data: 'operator.name',
        render: function(data, type, row) {
          return data ? data : 'Not yet assigned';
        }
      },
      { data: 'status_badge' },
      { data: 'actions' }
    ],
    order: [[2, 'desc']],
    dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    // responsive: {
    //   details: {
    //     display: $.fn.dataTable.Responsive.display.modal({
    //       header: function(row) {
    //         return 'Details for ' + row.data().title;
    //       }
    //     }),
    //     type: 'column',
    //     renderer: $.fn.dataTable.Responsive.renderer.tableAll({
    //       tableClass: 'table'
    //     })
    //   }
    // }
  });
}

function showDateRange(value) {
  if (value === 'custom') {
    $('.custom-date-range').show();
  } else {
    $('.custom-date-range').hide();
    refreshTable();
  }
}

function refreshTable() {
  jobTable.draw();
}

</script>
<script>
// Existing DataTable initialization code...

// Function to open create modal
function openCreateModal() {
  // Reset form
  $('#createJobForm')[0].reset();
  // Clear any previous error messages
  $('.invalid-feedback').empty();
  $('.form-control').removeClass('is-invalid');
  // Open modal
  $('#createJobModal').modal('show');
}

// Initialize flatpickr
document.addEventListener('DOMContentLoaded', function() {
  flatpickr('.flatpickr-date-time', {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    minDate: "today",
    time_24hr: true
  });
});

// Handle form submission
$('#createJobForm').on('submit', function(e) {
  e.preventDefault();
  
  // Show loading spinner
  $('#submitJob .spinner-border').removeClass('d-none');
  $('#submitJob').attr('disabled', true);

  // Create FormData object
  const formData = new FormData(this);

  // Send AJAX request
  $.ajax({
    url: '{{ route("jobs.store") }}',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
      // Hide modal
      $('#createJobModal').modal('hide');
      
      // Show success message
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Job created successfully!'
      });

      // Refresh DataTable
      $('.datatables-jobs').DataTable().ajax.reload();
    },
    error: function(xhr) {
      // Handle validation errors
      if (xhr.status === 422) {
        const errors = xhr.responseJSON.errors;
        
        // Clear previous errors
        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');

        // Show new errors
        Object.keys(errors).forEach(field => {
          $(`#${field}`).addClass('is-invalid');
          $(`#${field}-error`).text(errors[field][0]);
        });
      } else {
        // Show error message
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to create job. Please try again.'
        });
      }
    },
    complete: function() {
      // Hide loading spinner
      $('#submitJob .spinner-border').addClass('d-none');
      $('#submitJob').attr('disabled', false);
    }
  });
});

// Add JavaScript functions for handling actions
function assignOperator(jobId) {
    // Get operators in planner's team
    $.get(`/jobs/${jobId}/operators`, function(operators) {
        Swal.fire({
            title: 'Assign Operator',
            html: `
                <select id="operator_id" class="form-control">
                    ${operators.map(op => `<option value="${op.id}">${op.name}</option>`).join('')}
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: 'Assign',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: `/jobs/${jobId}/assign-operator`,
                    type: 'POST',
                    data: {
                        operator_id: $('#operator_id').val(),
                        _token: '{{ csrf_token() }}'
                    }
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                jobTable.ajax.reload();
            }
        });
    });
}

function submitWork(jobId) {
    Swal.fire({
        title: 'Submit Completed Work',
        html: `
            <input type="file" id="completed_file" class="form-control">
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const formData = new FormData();
            formData.append('completed_file', $('#completed_file')[0].files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            return $.ajax({
                url: `/jobs/${jobId}/submit-work`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            jobTable.ajax.reload();
        }
    });
}

function reuploadWork(jobId) {
    // Get job details to show feedback
    $.get(`/jobs/${jobId}/comments`, function(comments) {
        let commentHtml = '';
        if (comments.length > 0) {
            comments.forEach(comment => {
                commentHtml += `
                    <div class="border-bottom mb-2 pb-2">
                        <p class="mb-1"><strong>${comment.user.name}</strong> - ${comment.created_at}</p>
                        <p>${comment.comment}</p>
                    </div>
                `;
            });
        } else {
            commentHtml = '<p>No feedback available</p>';
        }
        
        Swal.fire({
            title: 'Reupload Revised Work',
            html: `
                <div class="mb-3">
                    <h6>QC Feedback:</h6>
                    <div class="text-start">
                        ${commentHtml}
                    </div>
                </div>
                <div>
                    <h6>Upload Revised File:</h6>
                    <input type="file" id="completed_file" class="form-control">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                if (!$('#completed_file')[0].files[0]) {
                    Swal.showValidationMessage('Please select a file to upload');
                    return false;
                }
                
                const formData = new FormData();
                formData.append('completed_file', $('#completed_file')[0].files[0]);
                formData.append('_token', '{{ csrf_token() }}');

                return $.ajax({
                    url: `/jobs/${jobId}/reupload-work`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                }).catch(error => {
                    Swal.showValidationMessage(`Upload failed: ${error.responseJSON?.message || 'Unknown error'}`);
                });
            },
            width: '600px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Revised work submitted for QC review!'
                });
                jobTable.ajax.reload();
            }
        });
    });
}

function reviewWork(jobId) {
    Swal.fire({
        title: 'QC Review',
        html: `
            <select id="qc_status" class="form-control mb-2">
                <option value="approved">Approve</option>
                <option value="changes_requested">Request Changes</option>
            </select>
            <textarea id="qc_comment" class="form-control" placeholder="Comments (required for changes)" style="display: none;"></textarea>
        `,
        didOpen: () => {
            $('#qc_status').on('change', function() {
                $('#qc_comment').toggle($(this).val() === 'changes_requested');
            });
        },
        showCancelButton: true,
        confirmButtonText: 'Submit Review',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/jobs/${jobId}/qc-review`,
                type: 'POST',
                data: {
                    status: $('#qc_status').val(),
                    comment: $('#qc_comment').val(),
                    _token: '{{ csrf_token() }}'
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            jobTable.ajax.reload();
        }
    });
}

// New function to reassign a job to a different planner
function reassignPlanner(jobId) {
    // Get all available planners
    $.get(`/jobs/planners`, function(planners) {
        Swal.fire({
            title: 'Reassign Planner',
            html: `
                <p>Select a new planner to handle this job:</p>
                <select id="planner_id" class="form-control">
                    ${planners.map(planner => `<option value="${planner.id}">${planner.name}</option>`).join('')}
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: 'Reassign',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const plannerId = Swal.getPopup().querySelector('#planner_id').value;
                if (!plannerId) {
                    Swal.showValidationMessage('Please select a planner');
                    return false;
                }
                return $.ajax({
                    url: `/jobs/${jobId}/reassign-planner`,
                    type: 'POST',
                    data: {
                        planner_id: plannerId,
                        _token: '{{ csrf_token() }}'
                    }
                }).catch(() => {
                    Swal.showValidationMessage('Failed to reassign planner. Please try again.');
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Job reassigned to new planner successfully!'
                });
                jobTable.ajax.reload();
            }
        });
    });
}


function deleteJob(jobId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/jobs/${jobId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Job has been deleted.'
            });
            jobTable.ajax.reload();
        }
    }).catch((error) => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to delete job. Please try again.'
        });
    });
}

function viewFeedback(jobId) {
    $.get(`/jobs/${jobId}/comments`, function(comments) {
        let commentHtml = '';
        comments.forEach(comment => {
            commentHtml += `
                <div class="border-bottom mb-2 pb-2">
                    <p class="mb-1"><strong>${comment.user.name}</strong> - ${comment.created_at}</p>
                    <p>${comment.comment}</p>
                </div>
            `;
        });
        
        Swal.fire({
            title: 'QC Feedback',
            html: `
                <div class="text-start">
                    ${commentHtml || '<p>No feedback available</p>'}
                </div>
            `,
            width: '600px'
        });
    });
}

function deliverJob(jobId) {
    Swal.fire({
        title: 'Deliver to Client',
        html: `
            <p>Are you sure you want to mark this job as delivered to the client?</p>
        `,
        showCancelButton: true,
        confirmButtonText: 'Yes, Deliver',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/jobs/${jobId}/deliver`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Job has been marked as delivered!'
            });
            jobTable.ajax.reload();
        }
    }).catch((error) => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to deliver job. Please try again.'
        });
    });
}

function viewJobDetails(jobId) {
    // Show job details in a modal
    $.get(`/jobs/${jobId}/details`, function(job) {
        let filesHtml = '';
        if (job.files.length > 0) {
            job.files.forEach(file => {
                filesHtml += `
                    <div class="mb-1">
                        <strong>${file.file_type.charAt(0).toUpperCase() + file.file_type.slice(1)} File:</strong>
                        <a href="/storage/${file.file_path}" target="_blank" class="btn btn-sm btn-link">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                `;
            });
        } else {
            filesHtml = '<p>No files available</p>';
        }

        let commentsHtml = '';
        if (job.comments.length > 0) {
            job.comments.forEach(comment => {
                commentsHtml += `
                    <div class="border-bottom mb-2 pb-2">
                        <p class="mb-1"><strong>${comment.user.name}</strong> - ${comment.created_at}</p>
                        <p>${comment.comment}</p>
                    </div>
                `;
            });
        } else {
            commentsHtml = '<p>No comments available</p>';
        }

        Swal.fire({
            title: job.title,
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <p><strong>Client:</strong> ${job.client_name}</p>
                        <p><strong>Created By:</strong> ${job.creator ? job.creator.name : 'N/A'}</p>
                        <p><strong>Planner:</strong> ${job.planner ? job.planner.name : 'N/A'}</p>
                        <p><strong>Operator:</strong> ${job.operator ? job.operator.name : 'N/A'}</p>
                        <p><strong>Deadline:</strong> ${job.deadline}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(job.status)}">${getStatusLabel(job.status)}</span></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Description</h5>
                        <p>${job.description}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Files</h5>
                        ${filesHtml}
                    </div>
                    
                    <div class="mb-3">
                        <h5>Comments</h5>
                        ${commentsHtml}
                    </div>
                </div>
            `,
            width: '700px',
            confirmButtonText: 'Close'
        });
    });
}

function getStatusColor(status) {
    const statusColors = {
        'assigned_to_planner': 'warning',
        'assigned_to_operator': 'info',
        'in_qc': 'primary',
        'changes_requested': 'danger',
        'approved': 'success',
        'delivered': 'success'
    };
    
    return statusColors[status] || 'secondary';
}

function getStatusLabel(status) {
    const statusLabels = {
        'assigned_to_planner': 'Assigned to Planner',
        'assigned_to_operator': 'Assigned to Operator',
        'in_qc': 'In QC Review',
        'changes_requested': 'Changes Requested',
        'approved': 'Approved',
        'delivered': 'Delivered'
    };
    
    return statusLabels[status] || status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}
</script>
@endsection