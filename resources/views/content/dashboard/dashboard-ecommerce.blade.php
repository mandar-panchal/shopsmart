@extends('layouts/contentLayoutMaster')

@section('title', 'E-commerce Dashboard')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection

@section('page-style')
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/dashboard-ecommerce.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
@endsection

@section('content')
<section id="dashboard-ecommerce">
  
  {{-- Key Metrics Row --}}
  <div class="row match-height">
    <!-- Total Revenue Card -->
    <div class="col-xl-3 col-md-6 col-12">
      <div class="card earnings-card">
        <div class="card-body">
          <div class="row">
            <div class="col-8">
              <h4 class="card-title mb-1">Total Revenue</h4>
              <div class="font-small-2">This Month</div>
              <h5 class="mb-1">₹{{ number_format($revenueThisMonth, 2) }}</h5>
              <p class="card-text text-muted font-small-2">
                <span class="fw-bolder {{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                  {{ number_format(abs($revenueGrowth), 1) }}%
                </span>
                <span> {{ $revenueGrowth >= 0 ? 'increase' : 'decrease' }}</span>
              </p>
            </div>
            <div class="col-4">
              <div class="avatar bg-light-primary p-50">
                <div class="avatar-content">
                  <i data-feather="dollar-sign" class="font-large-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Orders Card -->
    <div class="col-xl-3 col-md-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-8">
              <h4 class="card-title mb-1">Total Orders</h4>
              <div class="font-small-2">This Month</div>
              <h5 class="mb-1">{{ $ordersThisMonth }}</h5>
              <p class="card-text text-muted font-small-2">
                <span class="fw-bolder {{ $orderGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                  {{ number_format(abs($orderGrowth), 1) }}%
                </span>
                <span> {{ $orderGrowth >= 0 ? 'increase' : 'decrease' }}</span>
              </p>
            </div>
            <div class="col-4">
              <div class="avatar bg-light-success p-50">
                <div class="avatar-content">
                  <i data-feather="shopping-cart" class="font-large-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Customers Card -->
    <div class="col-xl-3 col-md-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-8">
              <h4 class="card-title mb-1">Total Customers</h4>
              <div class="font-small-2">Registered</div>
              <h5 class="mb-1">{{ $totalCustomers }}</h5>
              <p class="card-text text-muted font-small-2">
                <span class="fw-bolder text-info">{{ $newCustomersThisMonth }}</span>
                <span> new this month</span>
              </p>
            </div>
            <div class="col-4">
              <div class="avatar bg-light-info p-50">
                <div class="avatar-content">
                  <i data-feather="users" class="font-large-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Average Order Value Card -->
    <div class="col-xl-3 col-md-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-8">
              <h4 class="card-title mb-1">Avg Order Value</h4>
              <div class="font-small-2">Per Order</div>
              <h5 class="mb-1">₹{{ number_format($avgOrderValue, 2) }}</h5>
              <p class="card-text text-muted font-small-2">
                <span class="fw-bolder">{{ $totalOrders }}</span>
                <span> total orders</span>
              </p>
            </div>
            <div class="col-4">
              <div class="avatar bg-light-warning p-50">
                <div class="avatar-content">
                  <i data-feather="trending-up" class="font-large-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Statistics Row --}}
  <div class="row match-height">
    <!-- Statistics Card -->
    <div class="col-xl-8 col-12">
      <div class="card card-statistics">
        <div class="card-header d-flex justify-content-between align-items-start pb-0">
          <div>
            <h4 class="card-title">Today's Statistics</h4>
            <p class="card-text font-small-2 me-25 mb-0">{{ now()->format('l, F d, Y') }}</p>
          </div>
        </div>
        <div class="card-body statistics-body">
          <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-primary me-2">
                  <div class="avatar-content">
                    <i data-feather="shopping-bag" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{ $ordersToday }}</h4>
                  <p class="card-text font-small-3 mb-0">Orders Today</p>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-success me-2">
                  <div class="avatar-content">
                    <i data-feather="dollar-sign" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">₹{{ number_format($revenueToday, 0) }}</h4>
                  <p class="card-text font-small-3 mb-0">Revenue Today</p>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-sm-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-info me-2">
                  <div class="avatar-content">
                    <i data-feather="user-plus" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{ $newCustomersToday }}</h4>
                  <p class="card-text font-small-3 mb-0">New Customers</p>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-warning me-2">
                  <div class="avatar-content">
                    <i data-feather="package" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{ $activeProducts }}</h4>
                  <p class="card-text font-small-3 mb-0">Active Products</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Stats -->
    <div class="col-xl-4 col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Product Inventory</h4>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
              <div class="avatar bg-light-success me-2">
                <div class="avatar-content">
                  <i data-feather="check-circle" class="avatar-icon"></i>
                </div>
              </div>
              <span>In Stock</span>
            </div>
            <h5 class="mb-0">{{ $activeProducts }}</h5>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
              <div class="avatar bg-light-warning me-2">
                <div class="avatar-content">
                  <i data-feather="alert-triangle" class="avatar-icon"></i>
                </div>
              </div>
              <span>Low Stock</span>
            </div>
            <h5 class="mb-0">{{ $lowStockProducts }}</h5>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <div class="avatar bg-light-danger me-2">
                <div class="avatar-content">
                  <i data-feather="x-circle" class="avatar-icon"></i>
                </div>
              </div>
              <span>Out of Stock</span>
            </div>
            <h5 class="mb-0">{{ $outOfStockProducts }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Charts Row --}}
  <div class="row match-height">
    <!-- Monthly Sales Chart -->
    <div class="col-xl-8 col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title">Monthly Sales Revenue</h4>
            <p class="card-text">Last 12 Months</p>
          </div>
        </div>
        <div class="card-body">
          <canvas id="monthlySalesChart" height="80"></canvas>
        </div>
      </div>
    </div>

    <!-- Order Status Pie Chart -->
    <div class="col-xl-4 col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Order Status</h4>
          <p class="card-text">Current Distribution</p>
        </div>
        <div class="card-body">
          <canvas id="orderStatusChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Top Products & Recent Customers --}}
  <div class="row match-height">
    <!-- Top Selling Products -->
    <div class="col-xl-6 col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Top Selling Products</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>SKU</th>
                  <th class="text-center">Sold</th>
                  <th class="text-end">Revenue</th>
                </tr>
              </thead>
              <tbody>
                @foreach($topProducts as $product)
                <tr>
                  <td>
                    <strong>{{ Str::limit($product->name, 30) }}</strong>
                  </td>
                  <td>{{ $product->sku }}</td>
                  <td class="text-center">
                    <span class="badge bg-light-primary">{{ $product->total_sold }}</span>
                  </td>
                  <td class="text-end">
                    <strong>₹{{ number_format($product->total_revenue, 2) }}</strong>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Customers -->
    <div class="col-xl-6 col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Top Customers by Spending</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th class="text-center">Orders</th>
                  <th class="text-end">Total Spent</th>
                </tr>
              </thead>
              <tbody>
                @foreach($topCustomers as $customer)
                <tr>
                  <td>
                    <div>
                      <strong>{{ $customer->name }}</strong>
                      <br>
                      <small class="text-muted">{{ $customer->email }}</small>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light-info">{{ $customer->total_orders }}</span>
                  </td>
                  <td class="text-end">
                    <strong class="text-success">₹{{ number_format($customer->total_spent, 2) }}</strong>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent Customers --}}
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Recent Customer Registrations</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Registered</th>
                  <th class="text-center">Orders</th>
                  <th class="text-end">Total Spent</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentCustomers as $customer)
                <tr>
                  <td>
                    <strong>{{ $customer->name }}</strong>
                  </td>
                  <td>{{ $customer->email }}</td>
                  <td>{{ $customer->phone }}</td>
                  <td>
                    <small>{{ $customer->created_at->format('M d, Y') }}</small>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light-primary">{{ $customer->orders_count }}</span>
                  </td>
                  <td class="text-end">
                    <strong>₹{{ number_format($customer->orders_sum_final_amount ?? 0, 2) }}</strong>
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $customer->email_verified_at ? 'bg-success' : 'bg-warning' }}">
                      {{ $customer->email_verified_at ? 'Verified' : 'Pending' }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Customer Activity --}}
  <div class="row match-height">
    <div class="col-lg-4 col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="fw-bolder">{{ $customerActivity['active_carts'] }}</h2>
              <p class="card-text">Active Shopping Carts</p>
            </div>
            <div class="avatar bg-light-primary p-50">
              <div class="avatar-content">
                <i data-feather="shopping-cart" class="font-large-2"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="fw-bolder">{{ $customerActivity['wishlist_items'] }}</h2>
              <p class="card-text">Wishlist Items</p>
            </div>
            <div class="avatar bg-light-danger p-50">
              <div class="avatar-content">
                <i data-feather="heart" class="font-large-2"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="fw-bolder">{{ $customerActivity['product_views_today'] }}</h2>
              <p class="card-text">Product Views Today</p>
            </div>
            <div class="avatar bg-light-info p-50">
              <div class="avatar-content">
                <i data-feather="eye" class="font-large-2"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>
@endsection

@section('vendor-script')
  <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Monthly Sales Chart
    var monthlySalesData = {!! json_encode($monthlySales) !!};
    var monthLabels = monthlySalesData.map(item => item.month);
    var revenueData = monthlySalesData.map(item => parseFloat(item.total_revenue));

    new Chart(document.getElementById('monthlySalesChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Revenue (₹)',
                data: revenueData,
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderColor: '#667eea',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Order Status Chart
    var orderStatusData = {!! json_encode($orderStatusBreakdown) !!};
    var statusLabels = orderStatusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1));
    var statusCounts = orderStatusData.map(item => item.count);
    var statusColors = {
        'pending': '#ffc107',
        'processing': '#17a2b8',
        'completed': '#28a745',
        'cancelled': '#dc3545'
    };
    var backgroundColors = orderStatusData.map(item => statusColors[item.status] || '#6c757d');

    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: backgroundColors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection