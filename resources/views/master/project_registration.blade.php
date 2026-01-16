@extends('layouts.contentLayoutMaster')
@section('title', 'Project Registration')

@section('content')
<section id="project-registration">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title"></h4>
                </div>
                <div class="card-body">
                 
                    <!-- Form -->
                    <form action="{{ route('project.store') }}" method="POST" id="projectForm">
                        @csrf
                        <div class="row">
                            <!-- Society Details Section -->
                            <div class="col-12 p-2 mb-3" style="background-color: #f8f9fa; border-radius: 5px;">
                                <h5 class="text-primary">Society Details</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="society_name" class="form-label">Society Name <span class="text-danger">*</span></label>
                                        <input type="text" id="society_name" name="society_name" class="form-control" value="{{ old('society_name') }}">
                                        <small id="society_name_error" class="text-danger"></small>
                                    </div>
                                      <div class="col-md-6 mb-2">
                                             <label for="contact_no" class="form-label">Contact No<span class="text-danger">*</span></label>
                                            <input type="text" id="contact_no" name="contact_no" class="form-control" value="{{ old('contact_no') }}" maxlength="10" oninput="validateMobile(this)">
                                             @error('contact_no') <!-- Display error message for contact_no -->
                                                 <small class="text-danger">{{ $message }}</small>
                                             @enderror
                                        </div>
                                    <div class="col-md-12 mb-2">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}">
                                    </div>
                                    <div class="col-md-12 mb-2">
                                    <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                                        <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                                            @error('address') <!-- Display error message for address -->
                                                 <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                    </div>

                                </div>
                            </div>
                            
                            <!-- President Section -->
                            <div class="col-12 p-2 mb-3" style="background-color: #e9f7ef; border-radius: 5px;">
                                <h5 class="text-success">President Details</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="president_name" class="form-label">President Name</label>
                                        <input type="text" id="president_name" name="president_name" class="form-control" value="{{ old('president_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="president_email" class="form-label">President Email</label>
                                        <input type="email" id="president_email" name="president_email" class="form-control" value="{{ old('president_email') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="president_no" class="form-label">President Contact No</label>
                                        <input type="text" id="president_no" name="president_no" class="form-control" value="{{ old('president_no') }}" maxlength="10" oninput="validateMobile(this)">
                                        <small id="president_no_error" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Vice President Section -->
                            <div class="col-12 p-2 mb-3" style="background-color: #fef9e7; border-radius: 5px;">
                                <h5 class="text-warning">Vice President Details</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="vice_president_name" class="form-label">Vice President Name</label>
                                        <input type="text" id="vice_president_name" name="vice_president_name" class="form-control" value="{{ old('vice_president_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="vice_president_email" class="form-label">Vice President Email</label>
                                        <input type="email" id="vice_president_email" name="vice_president_email" class="form-control" value="{{ old('vice_president_email') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="vice_president_no" class="form-label">Vice President Contact No</label>
                                        <input type="text" id="vice_president_no" name="vice_president_no" class="form-control" value="{{ old('vice_president_no') }}" maxlength="10" oninput="validateMobile(this)">
                                        <small id="vice_president_no_error" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Secretary Section -->
                            <div class="col-12 p-2 mb-3" style="background-color: #f9e7f7; border-radius: 5px;">
                                <h5 class="text-danger">Secretary Details</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="secretary_name" class="form-label">Secretary Name</label>
                                        <input type="text" id="secretary_name" name="secretary_name" class="form-control" value="{{ old('secretary_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="secretary_email" class="form-label">Secretary Email</label>
                                        <input type="email" id="secretary_email" name="secretary_email" class="form-control" value="{{ old('secretary_email') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="secretary_no" class="form-label">Secretary Contact No</label>
                                        <input type="text" id="secretary_no" name="secretary_no" class="form-control" value="{{ old('secretary_no') }}" maxlength="10" oninput="validateMobile(this)">
                                        <small id="secretary_no_error" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Real-Time Validation -->
<script>
    // Function to validate mobile number
    function validateMobile(input) {
        const errorField = document.getElementById(input.id + '_error');
        const value = input.value;
        if (value.length > 10 || isNaN(value)) {
            errorField.textContent = "Mobile number must be numeric and not exceed 10 digits.";
            input.value = value.slice(0, 10);
        } else {
            errorField.textContent = "";
        }
    }

    // Add an event listener to the 'society_name' field to monitor changes
    document.getElementById('society_name').addEventListener('input', function() {
        const inputField = this;
        
        // // Check if the entered value is "jeva"
        // if (inputField.value.toLowerCase() === '') {
        //     inputField.style.backgroundColor = 'lightgreen'; // Change background color to green
        // } else {
        //     inputField.style.backgroundColor = ''; // Reset the background color if not "jeva"
        // }
    });

    // Function to validate the form before submission
    document.getElementById('projectForm').addEventListener('submit', function (event) {
        var societyName = document.getElementById('society_name');
        var errorField = document.getElementById('society_name_error');
        
        // Reset previous error message and style
        errorField.textContent = '';
        societyName.classList.remove('is-invalid');
        
        // Check if the society name field is empty
        if (societyName.value.trim() === '') {
            errorField.textContent = 'This field is required'; // Display error message
            societyName.classList.add('is-invalid'); // Add red border
            event.preventDefault(); // Prevent form submission
        }
    });
</script>

<!-- SweetAlert for Success -->
@if (session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session("success") }}',
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

<!-- SweetAlert for Error -->
@if(session('error'))
<script>
    Swal.fire({
        title: 'Error!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'OK'
    });
</script>
@endif
@endsection
