@extends('layouts.contentLayoutMaster')

@section('title', 'Register Page')


@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
  <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-wizard.css')) }}">
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create Users</h4>
      </div>
      <div class="card-body">


          <form class="auth-register-form mt-2" method="POST" id="registrationForm">
            @csrf
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label for="register-name" class="form-label">Name <span style="color:red">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="register-name"
                    name="name" placeholder="Name" aria-describedby="register-name" tabindex="1" autofocus
                    value="{{ old('name') }}" />
                  @error('name')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label for="register-username" class="form-label">Username (Without any whitespace)<span style="color:red">*</span></label>
                  <input type="text" class="form-control @error('username') is-invalid @enderror" id="register-username"
                    username="username" placeholder="Username" name="username" aria-describedby="register-username" tabindex="1" autofocus
                    value="{{ old('username') }}" />
                  @error('username')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
            </div>
          <div class="row">
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label for="register-email" class="form-label">Email <span style="color:red">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="register-email"
                  name="email" placeholder="Email" aria-describedby="register-email" tabindex="2"
                  value="{{ old('email') }}" />
                @error('email')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="mb-1">
                    <label for="password" class="form-label">Password <span style="color:red">*</span></label>
                    <div class="input-group form-password-toggle ">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password" tabindex="3" />
                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label for="role" class="form-label">Select role<span style="color:red">*</span></label>
                  <select class="form-control form-select" id="role" required name="role">
                    
                  </select>             
                </div>
            
            </div>
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label for="register-extension" class="form-label">Extension</label>
                <input type="text" class="form-control @error('extension') is-invalid @enderror" id="register-extension"
                  name="extension" placeholder="Extension number" aria-describedby="register-extension" tabindex="2"
                  value="{{ old('extension') }}" />
                @error('extension')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label for="register-lead_target" class="form-label">Lead Target</label>
                <input type="number" class="form-control @error('lead_target') is-invalid @enderror" id="register-lead_target"
                  name="lead_target" placeholder="Lead target" aria-describedby="register-lead_target" tabindex="2"
                  value="{{ old('lead_target') }}" />
                @error('lead_target')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label for="register-incoming_visit_target" class="form-label">Incoming visit target</label>
                <input type="number" class="form-control @error('incoming_visit_target') is-invalid @enderror" id="register-incoming_visit_target"
                  name="incoming_visit_target" placeholder="Incoming visit target" aria-describedby="register-incoming_visit_target" tabindex="2"
                  value="{{ old('incoming_visit_target') }}" />
                @error('incoming_visit_target')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label for="register-popup_visit_target" class="form-label">Popup visit target</label>
                <input type="number" class="form-control @error('popup_visit_target') is-invalid @enderror" id="register-popup_visit_target"
                  name="popup_visit_target" placeholder="Popup visit target" aria-describedby="register-popup_visit_target" tabindex="2"
                  value="{{ old('popup_visit_target') }}" />
                @error('popup_visit_target')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
            <div class="col-md-6 col-12">
              <div class="mb-1">
                <label for="register-telecalling_target" class="form-label">Telecalling target</label>
                <input type="number" class="form-control @error('telecalling_target') is-invalid @enderror" id="register-telecalling_target"
                  name="telecalling_target" placeholder="Telecalling target" aria-describedby="register-telecalling_target" tabindex="2"
                  value="{{ old('telecalling_target') }}" />
                @error('telecalling_target')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
            </div>
          </div>


            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
              <div class="mb-1">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="terms" name="terms" tabindex="4" />
                  <label class="form-check-label" for="terms">
                    I agree to the <a href="{{ route('terms.show') }}" target="_blank">terms_of_service</a> and
                    <a href="{{ route('policy.show') }}" target="_blank">privacy_policy</a>
                  </label>
                </div>
              </div>
            @endif
            <div class="col-md-3 col-12">
              <button type="submit" class="btn btn-primary w-100" tabindex="5">Create user</button>
            </div>
          </form>
        </div>
      </div>
      <!-- /Register basic -->
    </div>
  </div>

 
  
  @endsection
  @section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/forms/wizard/bs-stepper.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
<script>
  $(document).ready(function () {
    $.ajax({
        url: '/authorization/get-roles',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Assuming you have a select element with the ID 'role'
            var select = $('#role');

            // Clear existing options
            select.empty();

            // Populate options from the fetched roles
            $.each(data.roles, function (index, role) {
                select.append($('<option>', {
                    value: role.name,
                    text: role.name,
                }));
            });
        },
        error: function (xhr, status, error) {
            console.error('Error fetching roles:', error);
        },
    });
  });
</script>
<script>
  $(document).ready(function() {
    $('#registrationForm').submit(function(event) {
      // Prevent the default form submission
      event.preventDefault();

      // Serialize the form data
      var formData = $(this).serialize();

      // Make the AJAX request
      $.ajax({
        type: 'POST',
        url: '/users/create',
        data: formData,
        success: function(response) {
          showSweetAlert('Updated!', response.message, function() {
                location.reload();
            });  
        },
        error: function(error) {
          showSweetAlertError('Error!', error.responseJSON.message, function() {
                 location.reload();
                }); 
        }
      });
    });
  });
</script>
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/forms/form-wizard.js')) }}"></script>
@endsection