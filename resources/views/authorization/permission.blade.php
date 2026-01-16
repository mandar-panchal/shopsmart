@extends('layouts/contentLayoutMaster')

@section('title', 'Permission')

@section('vendor-style')
  <!-- Vendor css files -->
  
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
  
@endsection
@section('page-style')
  <!-- Page css files -->
  
  <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')
<h3>Permissions List</h3>
<p>Each category (Basic, Professional, and Business) includes the four predefined roles shown below.</p>

<!-- Permission Table -->
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-permissions table">
      <thead class="table-light">
        <tr>
          <th></th>
          <th></th>
          <th>Sr no</th>
          <th>Name</th>
          <th>Assigned To</th>
          <th>Created Date</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Permission Table -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-sm-5 pb-5">
        <div class="text-center mb-2">
          <h1 class="mb-1">Add New Permission</h1>
        </div>
        <form id="addPermissionForm" class="row" onsubmit="return false">
          <div class="col-12">
            <label class="form-label" for="modalPermissionName">Permission Name</label>
            <input type="text" id="modalPermissionName" name="modalPermissionName" class="form-control" placeholder="Permission Name" autofocus data-msg="Please enter permission name" />
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary mt-2 me-1">Create Permission</button>
            <button type="reset" class="btn btn-outline-secondary mt-2" data-bs-dismiss="modal" aria-label="Close">
              Discard
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


@endsection


@section('vendor-script')
  <!-- Vendor js files -->
  <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

@endsection

@section('page-script')

<!--/ Add new Permission via ajax call -->
<script>
  $(document).ready(function () {
    $('#addPermissionForm').submit(function (event) {
      event.preventDefault();
      
      var permissionName = $('#modalPermissionName').val();

      // Perform AJAX call to create a new permission
      $.ajax({
        url: '/authorization/permissions/create',
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          name: permissionName,
        },
        success: function (response) {
          showSweetAlert('Created!', response.message, function () {
            $('#addPermissionModal').modal('hide');
            location.reload();
          });
        },
        error: function (error) {
          showSweetAlertError('Error!', error.responseJSON.message, function () {
            $('#addPermissionModal').modal('hide');
            });
        },
      });
    });
  });
</script>
<script src="{{ asset(mix('js/scripts/pages/app-access-permission.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3 pt-0">
        <div class="text-center mb-2">
          <h1 class="mb-1">Edit Permission</h1>
          <p>Edit permission as per your requirements.</p>
        </div>

        <div class="alert alert-warning" role="alert">
          <h6 class="alert-heading">Warning!</h6>
          <div class="alert-body">
            By editing the permission name, you might break the system permissions functionality. Please ensure you're
            absolutely certain before proceeding.
          </div>
        </div>

        <form id="editPermissionForm" class="row" onsubmit="return false">
          <div class="col-sm-9">
            <label class="form-label" for="editPermissionName">Permission Name</label>
            <input type="text" id="editPermissionName" name="editPermissionName" class="form-control" placeholder="Enter a permission name" tabindex="-1" data-msg="Please enter permission name" />
          </div>
          <div class="col-sm-3 ps-sm-0">
            <button type="submit" class="btn btn-primary mt-2">Update</button>
          </div>
          <div class="col-12 mt-75">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="editCorePermission" />
              <label class="form-check-label" for="editCorePermission"> Set as core permission </label>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!--/ Add new Permission via ajax call -->

<!-- Edit Permission Modal Start-->

<script>
  $(document).ready(function () {
    // Edit Permission Modal - Open Modal and Fetch Data
    $('#editPermissionModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var permissionId = button.data('permission-id');

      // Perform AJAX call to get permission data
      $.ajax({
        url: '/authorization/permissions/' + permissionId + '/edit',
        type: 'GET',
        success: function (response) {
          // Populate data into the modal
          $('#editPermissionName').val(response.data.name);

          // Store permissionId in a data attribute of the modal for later use
          $('#editPermissionModal').data('permission-id', permissionId);
        },
        error: function (error) {
          console.error(error.responseText);
        }
      });
    });

    // Edit Permission Form Submission
    $('#editPermissionForm').submit(function (event) {
    event.preventDefault();

    var permissionId = $('#editPermissionModal').data('permission-id');
    var permissionName = $('#editPermissionName').val();

    // Update the specific cell in the table
    $('#row_' + permissionId + ' td:eq(2)').text(permissionName);

    // Optionally, you can make an AJAX call to update the permission on the server
    $.ajax({
      url: '/authorization/permissions/' + permissionId + '/update',
      type: 'POST',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        editPermissionName: permissionName,
      },
      success: function (response) {
        showSweetAlert('Updated!', response.message, function () {
          // Close the modal
          $('#editPermissionModal').modal('hide');
          location.reload();
        });
      },
      error: function (error) {
        showSweetAlertError('Error!', error.responseJSON.message, function () {
          // Optionally handle errors
        });
      },
    });
  });

  $('.datatables-permissions tbody').on('click', '.delete-record', function () {
    var permissionId = $(this).data('permission-id');

    // Show a confirmation SweetAlert
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // If the user clicks "Yes," proceed with the deletion
            deletePermission(permissionId);
        }
    });
});

function deletePermission(permissionId) {
    // Make AJAX call to delete permission
    $.ajax({
        url: '/authorization/permissions/' + permissionId + '/delete',
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (response) {
          showSweetAlert('Deleted!', response.message, function () {
          
        });
        },
        error: function (error) {
          showSweetAlertError('Error!', error.responseJSON.message, function () {
        
        });
        }
    });
}

  });
</script>

@endsection
