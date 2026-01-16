@extends('layouts/contentLayoutMaster')

@section('title', 'Roles')

@section('vendor-style')
  <!-- Vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection


@section('content')
<h3>Roles List</h3>
<!-- Role cards -->
<div class="row">



  @foreach ($roles as $role)
  <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card">
          <div class="card-body">
              <div class="d-flex justify-content-between">
                  <span>Total {{ $role->users->count() }} users</span>
                  <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                      {{-- Loop through users associated with the role --}}
                      @foreach ($role->users as $user)
                          <li
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              title="{{ $user->name }}"
                              class="avatar avatar-sm pull-up"
                          >
                              
                              <img class="rounded-circle" src="{{ asset('storage/'.$user->profile_photo_path) }}" alt="Avatar" />
              
                          </li>
                      @endforeach
                  </ul>
              </div>
              <div class="d-flex justify-content-between align-items-end mt-1 pt-25">
                  <div class="role-heading">
                      <h4 class="fw-bolder">{{ $role->name }}</h4>
                      <a href="javascript:;" class="role-edit-modal" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-role-id="{{ $role->id }}">
                          <small class="fw-bolder">Edit Role</small>
                      </a>
                  </div>
                  <a href="javascript:void(0);" class="text-body"><i data-feather="copy" class="font-medium-5"></i></a>
              </div>
          </div>
      </div>
  </div>
@endforeach
 
  
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="row">
        <div class="col-sm-5">
          <div class="d-flex align-items-end justify-content-center h-100">
            <img
              src="{{asset('images/illustration/faq-illustrations.svg')}}"
              class="img-fluid mt-2"
              alt="Image"
              width="85"
            />
          </div>
        </div>
        <div class="col-sm-7">
          <div class="card-body text-sm-end text-center ps-sm-0">
            <a
              href="javascript:void(0)"
              data-bs-target="#nameRoleModal"
              data-bs-toggle="modal"
              class="stretched-link text-nowrap add-new-role"
            >
              <span class="btn btn-primary mb-1">Add New Role</span>
            </a>
            <p class="mb-0">Add role, if it does not exist</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Role cards -->

<!-- table -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header border-bottom">
        <h4 class="card-title">Total users with their roles</h4>
      </div>
      <div class="card-datatable">
        <table class="user-list-table table" id="user-list-table">
          <thead class="table-light">
            <tr>
              <th></th>
              <th>Name</th>
              <th>Username</th>
              <th>Role</th>
              <th>Extension</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- table -->



<!-- Update user role modal -->
<div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateRoleModalLabel">Update User Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateRoleForm">
          <div class="mb-3">
            <label for="userRole" class="form-label">Select Role</label>
            <select class="form-select" id="userRole" required>
             
              <!-- Roles will be dynamically populated here using JavaScript -->
            </select>
          </div>
          <button type="submit" class="btn btn-primary" id="submitUpdateRoleBtn">Update Role</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- / Update user role modal -->

<!-- Edit role modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editRoleForm">
          <div class="mb-3">
            <label for="name" class="form-label">Role name</label>
            <input class="form-control" id="name" name="name" required value="">
          </div>
          <div class="mb-3">
            <label class="form-label" for="update_permissions">Permissions <span>(if permission not found close and edit again.)</span></label>
            <select class="select2 form-select" id="update_permissions" name="permissions[]" multiple>
          
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Edit</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- / Edit role modal -->

<!-- Create new role modal -->
<div class="modal fade" id="nameRoleModal" tabindex="-1" aria-labelledby="nameRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="nameRoleModalLabel">Create Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="nameRoleForm">
          <div class="mb-3">
            <label for="name" class="form-label">Role name</label>
            <input class="form-control" id="name" name="name" required >
          </div>
          
          <div class="mb-3">
            <label class="form-label" for="permissions">Permissions</label>
            <select class="select2 form-select" id="permissions" name="permissions[]" multiple>
       
              </select>
          </div>
          <button type="submit" class="btn btn-primary">Create</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- / Create new role modal -->

{{-- <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
  <div class="row">
      <div class="col-md-8 offset-md-2">
          <div class="card">
              <div class="card-header">Add New Role</div>

              <div class="card-body">
                <form id="updateRoleForm">
                      @csrf

                      <div class="form-group">
                          <label for="role_name">Role Name</label>
                          <input type="text" name="role_name" id="role_name" class="form-control" required>
                      </div>

                      <div class="form-group">
                          <label for="permissions">Role Permissions</label>
                          @foreach($permissions as $permission)
                              <div class="form-check">
                                  <input type="checkbox" id="permissions" name="permissions[]" value="{{ $permission->name }}">
                                  <label>{{ $permission->name }}</label>
                              </div>
                          @endforeach
                      </div>

                      <button type="submit" class="btn btn-primary">Create Role</button>
                  </form>
              </div>
          </div>
      </div>
  </div>
</div> --}}

@endsection

@section('vendor-script')
  <!-- Vendor js files -->
  <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>  
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  {{-- <script src="{{ asset(mix('js/scripts/pages/modal-add-role.js')) }}"></script> --}}

  
<script>
      $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
//User table script
        $(document).ready(function () {
        var dtUserTable = $('#user-list-table').DataTable({
            
            processing: true,
            serverSide: true,
            ajax: {
                url: '/users',
                type: 'POST',
                data: function (d) {
                    d.search = $('#global_search').val(); // Use a single input for global search
                },
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'username', name: 'username' },
                { data: 'roles', name: 'roles' },
                { data: 'extension', name: 'extension' },
                { data: 'status', name: 'status' },
                {
                    // Action column
                    data: null,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return '<button class="btn btn-sm btn-info update-btn" data-user-id="' + full.id + '" data-roles-name="' + full.roles + '">Update</button>';
                    },
                },
                // Add more columns as needed
            ],
          // Add other DataTable configurations as needed
          });
          $('#updateRoleForm').data('dtUserTable', dtUserTable);
          $('.dataTables_filter input').attr('id', 'global_search');

          // Add event listener for global search input
          $('#global_search').keyup(function () {
              dtUserTable.search($(this).val()).draw();
           
          });
  });
</script>


<!--/ Getting roles in select box to change user role script and update script -->
<script>
    $(document).on('click', '.update-btn', function () {
      var userId = $(this).data('user-id');
      $('#submitUpdateRoleBtn').data('user-id', userId);
      var currentRoleName = $(this).data('roles-name');
     // var currentRoleName = $(this).closest('tr').data('roles-name')


      // Fetch the list of roles from the server
      $.ajax({
        url: '/authorization/get-roles',
        type: 'GET',
        success: function (response) {
          var roles = response.roles;
  
          // Populate the select options in the modal
   
          var selectOptions = '';
          for (var i = 0; i < roles.length; i++) {
           
            var isSelected = roles[i].name === currentRoleName ? 'selected' : '';
                selectOptions += '<option value="' + roles[i].id + '" ' + isSelected + '>' + roles[i].name + '</option>';
          
          }
  
          // Update the HTML of the role select element in the modal
          $('#userRole').html(selectOptions);
  
          // Show the modal
          $('#updateRoleModal').modal('show');
        },
        error: function (error) {
          console.error('Error fetching roles:', error);
        },
      });
    });
  
    // Handle form submission
    $('#submitUpdateRoleBtn').click(function (event) {
      event.preventDefault();
      var userId = $(this).data('user-id');
      var roleId = $('#userRole').val();
  
      // Update the user role via AJAX
      $.ajax({
        url: '/update-role/' + userId,
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          role_id: roleId,
        },
        success: function (response) {

          showSweetAlert('Upadted!', response.message, function() {
            $('#updateRoleModal').modal('hide');
          location.reload();
            });  
       
        
          // You may need to reload the DataTable or update the row based on your logic
         
        },
        error: function (error) {
          showSweetAlertError('Error!', error.responseJSON.message, function() {
            $('#updateRoleModal').modal('hide');
            });  
         
        },
      });
    });
  </script>

  <!--/ Add new Role via ajax call -->
  {{-- <script>
      $('#nameRoleForm').submit(function (event) {
        event.preventDefault();

        // Get the role name from the input field
        var roleName = $('#roleName').val();

        // Perform AJAX call to create a new role
        $.ajax({
          url: '/authorization/roles/create',
          type: 'POST',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            name: roleName,
          },
          success: function (response) {
            showSweetAlert('Created!', response.message, function() {
              $('#nameRoleModal').modal('hide'); 
              location.reload();
            });          
          },
          error: function (error) {
            showSweetAlertError('Error!', error.responseJSON.message, function() {
            $('#nameRoleModal').modal('hide');
            });  
          },
        });
      });
    </script> --}}
    
<script>
  $(document).ready(function () {
      
      $.get('/authorization/permission/getpermissions', function (data) {
          var permissionsSelect = $('#permissions');
          permissionsSelect.empty();

          $.each(data, function (key, permission) {
              permissionsSelect.append($('<option></option>').val(permission.name).text(permission.name));
          });
      });

      $('#nameRoleForm').submit(function (e) {
          e.preventDefault();
          var formData = $(this).serialize();
          $.ajax({
              url: '/authorization/roles/create',
              type: 'POST',
              data: formData,
              success: function (response) {
                showSweetAlert('Created!', response.message, function() {
                  $('#nameRoleModal').modal('hide'); 
                  location.reload();
                  });     
              },
              error: function (error) {
                showSweetAlertError('Error!', error.responseJSON.message, function() {
                  $('#nameRoleModal').modal('hide');
                  location.reload();
                });  
              }
          });
      });
  });
</script>
<script>
  $(document).ready(function () {
      var roleId; // Declare roleId variable in a higher scope

      var allPermissions;

      console.log('Before fetching permissions:', allPermissions);

// Fetch permissions
$.get('/authorization/permission/getpermissions', function (data) {
    allPermissions = data;
});
      // Function to open the edit role modal and fetch role details
      function openEditRoleModal(id) {
    $.ajax({
        type: 'GET',
        url: '/authorization/roles/' + id,
        success: function (data) {

            // Populate the form fields with role details
            $('#editRoleForm #name').val(data.name);

            // Clear existing options in the select
            $('#editRoleForm #update_permissions').empty();

            if (Array.isArray(allPermissions)) {

                // Populate the select with permissions and mark selected ones
                allPermissions.forEach(function (permission) {
                    var isSelected = data.permissions.some(function (dataPermission) {
                        return dataPermission.id === permission.id;
                    });

                    // Append the option to the select
                    $('#editRoleForm #update_permissions').append('<option value="' + permission.name + '"' + (isSelected ? ' selected' : '') + '>' + permission.name + '</option>');
                });
            } else {
              showSweetAlert('Wait for a while !', response.message, function() {                 
                  location.reload();
                  });  
            }

            // Open the modal
            $('#editRoleModal').modal('show');
        },
        error: function (error) {
            console.error('Error fetching role details:', error);
        }
    });
}
      // Event listener for opening the edit role modal
      $('#editRoleModal').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget); // Button that triggered the modal
          roleId = button.data('role-id'); // Set the global roleId variable

          // Open the modal and fetch role details
          openEditRoleModal(roleId);
      });

      // Form submission logic (replace with your actual endpoint)
      $('#editRoleForm').submit(function (e) {
          e.preventDefault();

          $.ajax({
              type: 'POST',
              url: '/authorization/roles/update/' + roleId, // Use the global roleId variable
              data: $(this).serialize(),
              success: function (response) {
                showSweetAlert('Updated!', response.message, function() {
                  $('#editRoleModal').modal('hide');
                  location.reload();
                  });                   
              },
              error: function (error) {
                showSweetAlertError('Error!', error.responseJSON.message, function() {
                  $('#editRoleModal').modal('hide');
                  location.reload();
                }); 
              }
          });
      });
  });
</script>

  {{-- <script src="{{ asset(mix('js/scripts/pages/app-access-roles.js')) }}"></script> --}}
  <script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
@endsection

