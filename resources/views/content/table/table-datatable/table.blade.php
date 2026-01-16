

@extends('layouts/contentLayoutMaster')

@section('title', 'DataTables')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
@endsection


@section('content')
<!-- Ajax Sourced Server-side -->
<div class="container border p-2">
  <div class="row">
    <div class="col">
        <label for="center">Center</label>
        <br />
        <select name="center" id="center" class="form-control">
          <option>Nashik</option>
          <option>Thane</option>
          <option>pune</option>
          <option>Panvel</option>
        </select>
    </div>
    
    <div class="col">
        <label for="pt_type">Patient type</label>
        <br />
        <select name="pt_type" id="pt_type" class="form-control">
          <option>Male</option>
          <option>Female</option>
        </select>
    </div>
    
  </div>
  
  <div class="row">
    <div class="col">
        <label for="primary_source">Primary Source</label>
        <br />
        <select name="primary_source" id="primary_source" class="form-control">
          <option>DM</option>
          <option>TV</option>
          <option>Newspaper</option>
        </select>
    </div>
    
    <div class="col">
        <label for="secondary_source">Secondary Source</label>
        <br />
        <select name="secondary_source" id="secondary_source" class="form-control">
          <option>Facebook</option>
          <option>Google</option>
        </select>
    </div>
    
    
  </div>
  
  <div class="row p-1">
      <div class="col border m-1 p-2">
        <h3>Wife Info</h3>
        
        <label for="fname">First Name</label>
        <input type="text" name="wife_fname" id="wife_fname" value="" placeholder="First Name" class="form-control">
        
        <label for="lname">Last Name</label>
        <input type="text" name="wife_lname" id="wife_lname" value="" placeholder="Last Name" class="form-control">
        
        <label for="wife_age">Age</label>
        <input type="number" name="wife_age" id="wife_age" value="" placeholder="Age" class="form-control">
        
        <label for="wife_contact">Contact number</label>
        <input type="number" name="wife_contact" id="wife_contact" value="" placeholder="Contact" class="form-control">
        
      </div>
      
      <div class="col border m-1 p-2">
        <h3>Husband Info</h3>
        
        <label for="fname">First Name</label>
        <input type="text" name="wife_fname" id="wife_fname" value="" placeholder="First Name" class="form-control">
        
        <label for="lname">Last Name</label>
        <input type="text" name="wife_lname" id="wife_lname" value="" placeholder="Last Name" class="form-control">
        
        <label for="husband_age">Age</label>
        <input type="number" name="husband_age" id="husband_age" value="" placeholder="Age" class="form-control">
        
        <label for="husband_contact">Contact number</label>
        <input type="number" name="husband_contact" id="husband_contact" value="" placeholder="Contact" class="form-control">
        
      </div>
      
    </div>
    
    <div class="row">
      <div class="col">
        <label for="Marriage Since (In Years)">Marriage Since (In Years)</label>
        <br />
        <input type="number" name="marriage_since" id="marriage_since" value="" class="form-control">
      </div>
      
      <div class="col">
        
      </div>
    </div>
    
    <br />
    
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
      Advance Inforamtion
    </a>
    <br />
    <div class="collapse border p-2" id="collapseExample">
      
      <div class="row">
        <div class="col">
            <label for="country">Country</label>
            <br />
            <select name="country" id="country" class="form-control">
              <option>India</option>
              <option>France</option>
            </select>
        </div>
        
        <div class="col">
            <label for="state">State</label>
            <br />
            <select name="state" id="state" class="form-control">
              <option>Maharashtra</option>
              <option>Gujrat</option>
            </select>
        </div>
      </div>
      
      <div class="row">
        <div class="col">
            <label for="district">District</label>
            <br />
            <select name="district" id="district" class="form-control">
              <option>Nashik</option>
              <option>Ahmednagar</option>
            </select>
        </div>
        
        <div class="col">
            <label for="taluka">Taluka</label>
            <br />
            <select name="taluka" id="taluka" class="form-control">
              <option>Dindori</option>
              <option>Nashik</option>
            </select>
        </div>
      </div>
      
      <div class="row">
        <div class="col">
            <label for="city">City</label>
            <br />
            <select name="city" id="city" class="form-control">
              <option>Dindori</option>
              <option>Nashik</option>
            </select>
        </div>
        
        <div class="col">
            <label for="taluka">Taluka</label>
            <br />
            <textarea name="advance_info" id="" rows="8" cols="40" class="form-control" placeholder="Advance Information"></textarea>
        </div>
      </div>
      
    </div>
    
  
</div>
@endsection


@section('vendor-script')
{{-- vendor files --}}
  <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection

@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/tables/table-datatables-advanced.js')) }}"></script>
@endsection
