@extends('layouts/fullLayoutMaster')

@section('title', 'Login')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
  <div class="auth-wrapper auth-basic px-2">
    <div class="auth-inner my-2">
      <!-- Login basic -->
      <div class="card mb-0">
        <div class="card-body">
          <a href="#" class="brand-logo">
            <img src="/images/logo/logo1.png" height="36"/>
            <h2 class="brand-text text-primary ms-1">{{ config('app.name') }}</h2>
          </a>

          <h4 class="card-title mb-1">Login</h4>

          @if (session('status'))
            <div class="alert alert-success mb-1 rounded-0" role="alert">
              <div class="alert-body">
                {{ session('status') }}
              </div>
            </div>
          @endif

          <form class="auth-login-form mt-2" method="POST" action="#" id="login-form">
            @csrf
            <div class="mb-1">
              <label for="login-loginid" class="form-label">Username / Email<span style="color:red">*</span></label>
              <input type="text" class="form-control" id="login-loginid" name="email"
                placeholder="Enter username or email" aria-describedby="login-loginid" required tabindex="1" autofocus
                value="{{ old('loginid') }}" />
              
                <span class="invalid-feedback" role="alert">
                  <strong></strong>
                </span>
            
            </div>

            <div class="mb-1">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="login-password">Password<span style="color:red">*</span></label>
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}">
                    <small>Forgot Password?</small>
                  </a>
                @endif
              </div>
              <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge" id="login-password" name="password"
                  tabindex="2" required placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="login-password" />
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>

                 
              </div>
            </div>
             <div class="mb-1">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" tabindex="3"
                  {{ old('remember') ? 'checked' : '' }} />
                <label class="form-check-label" for="remember"> Remember Me </label>
              </div>
            </div>
            <button type="button" class="btn btn-primary w-100 submit" tabindex="4" onclick="submitForm()">Sign in</button>
          </form>
        </div>
      </div>
      <!-- /Login basic -->
    </div>
  </div>

@endsection

@section('page-script')

<script>
  function submitForm() {
      var $button = $('.submit');
      $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in...');

      $.ajax({
          url: '/api/login',
          method: 'POST',
          data: $('#login-form').serialize(),
          success: function(response) {
              const redirect = response.intended_url;
              document.cookie = 'authToken='+ response.token +'; path=/';
              window.location.href = redirect;
          },
          error: function(xhr, status, error) {
              var response = JSON.parse(xhr.responseText);
              if (response && response.message) {
                  showSweetAlertError('Login Error', response.message);
                  $('#login-password').val('');
              } else {
                  console.error('Error: ', status, error);
              }
          },
          complete: function() {
              $button.prop('disabled', false).html('Sign in');
          }
      });
  }
</script>

<script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection