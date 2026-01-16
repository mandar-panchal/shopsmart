@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-card.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stats-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stats-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.9;
    }
    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .order-item {
        border-bottom: 1px solid #eee;
        padding: 1rem 0;
    }
    .order-item:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
            <p class="text-muted">Welcome back, {{ auth('customer')->user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Total Orders</div>
                        <div class="stats-value">{{ $totalOrders }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Total Spent</div>
                        <div class="stats-value">₹{{ number_format($totalSpent, 2) }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Cart Items</div>
                        <div class="stats-value">{{ $cartCount }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Wishlist</div>
                        <div class="stats-value">{{ $wishlistCount }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="mb-4"><i class="fas fa-chart-line me-2"></i>Monthly Spending (2024)</h5>
                <canvas id="monthlySpendingChart" height="80"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-container">
                <h5 class="mb-4"><i class="fas fa-chart-pie me-2"></i>Order Status</h5>
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Top Products -->
    <div class="row">
        <div class="col-lg-7">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Orders</h5>
                    <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>

                @forelse($recentOrders as $order)
                    <div class="order-item">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <strong>{{ $order->order_number }}</strong>
                                <br>
                                <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                            </div>
                            <div class="col-md-3">
                                <strong>₹{{ number_format($order->final_amount, 2) }}</strong>
                            </div>
                            <div class="col-md-3">
                                {!! $order->status_badge !!}
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="{{ route('customer.orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #dee2e6;"></i>
                        <p class="text-muted mt-2">No orders yet</p>
                        <a href="{{ route('customer.products') }}" class="btn btn-primary btn-sm">
                            Start Shopping
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-5">
            <div class="chart-container">
                <h5 class="mb-4"><i class="fas fa-star me-2"></i>Top Products</h5>
                <div id="top-products-container">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    loadAnalytics();

    function loadAnalytics() {
        showLoader();
        
        $.ajax({
            url: "{{ route('customer.analytics') }}",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderMonthlySpendingChart(response.monthly_spending);
                    renderOrderStatusChart(response.status_breakdown);
                    renderTopProducts(response.top_products);
                }
            },
            error: function(xhr) {
                console.error('Analytics Error:', xhr);
                Swal.fire('Error!', 'Failed to load analytics', 'error');
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    function renderMonthlySpendingChart(data) {
        const ctx = document.getElementById('monthlySpendingChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.month),
                datasets: [{
                    label: 'Spending (₹)',
                    data: data.map(item => item.amount),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
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
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    function renderOrderStatusChart(data) {
        const ctx = document.getElementById('orderStatusChart').getContext('2d');
        
        const labels = data.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1));
        const values = data.map(item => item.count);
        const colors = {
            'pending': '#ffc107',
            'processing': '#17a2b8',
            'completed': '#28a745',
            'cancelled': '#dc3545'
        };
        const backgroundColors = data.map(item => colors[item.status] || '#6c757d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
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
    }

    function renderTopProducts(products) {
        const container = $('#top-products-container');
        
        if (products.length === 0) {
            container.html(`
                <div class="text-center py-3">
                    <i class="fas fa-box" style="font-size: 2rem; color: #dee2e6;"></i>
                    <p class="text-muted mt-2">No purchases yet</p>
                </div>
            `);
            return;
        }

        let html = '<div class="list-group list-group-flush">';
        products.forEach((product, index) => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-primary rounded-pill me-2">${index + 1}</span>
                        <strong>${product.name}</strong>
                        <br>
                        <small class="text-muted">${product.total_quantity} items purchased</small>
                    </div>
                    <div class="text-end">
                        <strong class="text-success">₹${parseFloat(product.total_spent).toFixed(2)}</strong>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        container.html(html);
    }
});
</script>
@endsection