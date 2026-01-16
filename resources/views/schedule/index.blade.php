

@extends('layouts/contentLayoutMaster')

@section('title', 'Scheduled list')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endsection

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
@endsection


@section('content')

<div class="col-md-12 col-12">
  <div class="card">
    <div class="card-header border-bottom bg-primary">
      <h4 class="card-title" style="color:whitesmoke">Fetch Schedule List</h4>
    </div>

  <div class="row p-2">

    <div class="col-md-3 mb-1">
      <label class="form-label">Search by</label>
      <select class="form-control form-select" onchange="show_dates(this.value)">
          <option value="1">Missed</option>
          <option value="1">Yesterday</option>
          <option value="1" selected>Today</option>
          <option value="1">Tomorrow</option>
          <option value="10">Custom date</option>
      </select>

      <button class="btn btn-primary mt-3">Submit</button>

    </div>

    <div class="col-md-9">
      <div class="row custom_date_div" style="display:none;">
        <div class="col-md-6">
          <label class="form-label">From date</label>
          <input type="date" name="" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">To date</label>
          <input type="date" name="" class="form-control">
        </div>
      </div>
    </div>
  </div>
</div>

  <div class="result_table">
    
  </div>

</div>



<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom bg-primary">
          <h4 class="card-title" style="color:whitesmoke">Scheduled Calls</h4>
        </div>
        <div class="card-datatable">
          <table class="datatables-ajax table table-responsive">
            <thead>
              <tr>
                <th>Name</th>
                <th>Added date</th>
                <th>Added by</th>
                <th>Wife no</th>
                <th>Husband no</th>
                <th>City</th>
                <th>Calling Status</th>
                <th>Important Status</th>
                <th>Called Count</th>
                <th>Updated by</th>
                <th>Call</th>             
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  function show_dates(value) {

    if (value == 10) {
      $('.custom_date_div').show();
    }else{
      $('.custom_date_div').hide();
    }

  }
</script>
<!-- Ajax Sourced Server-side -->
{{--  --}}
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
