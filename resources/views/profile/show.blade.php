@extends('layouts.contentLayoutMaster')

@php
$breadcrumbs = [['link' => 'home', 'name' => 'Home'], ['link' => 'javascript:void(0)', 'name' => 'User'], ['name' => 'Profile']];
@endphp

@section('title', 'Profile')

@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('content')

  @if (Laravel\Fortify\Features::canUpdateProfileInformation())
    @livewire('profile.update-profile-information-form')
  @endif

  @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
    @livewire('profile.update-password-form')
  @endif

  @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
    @livewire('profile.two-factor-authentication-form')
  @endif

  @livewire('profile.logout-other-browser-sessions-form')

  <section class="basic-select2">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Change Theme Option</h4>
          </div>
          <div class="card-body">
            <div class="row">
            
              <!-- Icons -->
              <div class="col-md-12 mb-1">
                <label class="form-label" for="select2-icons">Change Mode</label>
                <select data-placeholder="Select a theme option" class="select2-icons form-select" id="theme" name="theme">
                    <option value="dark-layout" data-icon="moon">Dark Mode</option>
                    <option value="light-layout" data-icon="sun">Light Mode</option>
                    <option value="semi-dark-layout" data-icon="sunset">Semi Dark Mode</option>                 
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> 
  @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
    @livewire('profile.delete-user-form')
  @endif
@endsection
@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>    
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
<script>
$(document).ready(function() {
    // Variable to store the current theme
    var currentTheme;
    // Fetch the current theme on page load
    $.ajax({
        url: '/get-current-theme',
        type: 'GET',
        success: function(response) {
            // Set the selected value in the dropdown
            currentTheme = response.currentTheme;
            $('#theme').val(currentTheme).trigger('change');
        },
        error: function(error) {
            console.error('Error fetching current theme');
        }
    });
    // Handle theme change
    $('#theme').on('change', function(e) {
        e.preventDefault();

        // Get the selected theme directly
        var newTheme = $(this).val();

        // Check if the theme has changed
        if (currentTheme !== newTheme) {
            // Make an AJAX request to update the theme mode
            $.ajax({
                url: '/update-theme-mode',
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'themeMode': newTheme
                },
                success: function(response) {
                    // Update the data-theme attribute and handle any UI changes
                    showSweetAlert('Updated!', response.message, function() {
                        // Reload the page only if necessary
                        if (response.reload) {
                            location.reload();
                        }
                    });

                    // Update the current theme
                    currentTheme = newTheme;
                },
                error: function(error) {
                    showSweetAlertError('Error!', error.responseJSON.message, function() {
                        // Reload the page only if necessary
                        if (error.responseJSON.reload) {
                            location.reload();
                        }
                    });
                }
            });
        }
    });
});
</script>
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
