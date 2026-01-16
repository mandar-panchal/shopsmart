@extends('layouts.app')
@section('title', 'My Orders')

@section('styles')
<style>
    .order-card {
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .order-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .order-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0;
    }
    .order-body {
        padding: 1.5rem;
    }
    .product-item {
        border-bottom: 1px solid #f0f0f0;
        padding: 1rem 0;
    }
    .product-item:last-child {
        border-bottom: none;
    }
    .product-image {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 5px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-box me-2"></i>My Orders</h2>
            <p class="text-muted">Track and manage your orders</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('customer.products') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-1"></i> Continue Shopping
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="search-input" placeholder="Search by order number...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="status-filter">
                        <option value="all">All Orders</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sort-filter">
                        <option value="latest">Latest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="amount_high">Amount: High to Low</option>
                        <option value="amount_low">Amount: Low to High</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100" id="reset-filters">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div id="orders-container">
        <!-- Orders will be loaded here -->
    </div>

    <div class="text-center py-5 d-none" id="loading-spinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
        <p class="mt-2">Loading orders...</p>
    </div>

    <div class="text-center py-5 d-none" id="empty-state">
        <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #dee2e6;"></i>
        <h4 class="mt-3">No orders found</h4>
        <p class="text-muted">You haven't placed any orders yet</p>
        <a href="{{ route('customer.products') }}" class="btn btn-primary mt-2">
            Start Shopping
        </a>
    </div>

    <!-- Pagination -->
    <nav class="mt-4 d-none" id="pagination-container">
        <ul class="pagination justify-content-center" id="pagination-links"></ul>
    </nav>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Order Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="order-detail">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let currentPage = 1;
    let searchTerm = '';
    let statusFilter = 'all';
    let sortBy = 'latest';

    loadOrders();

    $('#search-input').on('input', debounce(function() {
        searchTerm = $(this).val();
        currentPage = 1;
        loadOrders();
    }, 500));

    $('#status-filter, #sort-filter').on('change', function() {
        statusFilter = $('#status-filter').val();
        sortBy = $('#sort-filter').val();
        currentPage = 1;
        loadOrders();
    });

    $('#reset-filters').on('click', function() {
        $('#search-input').val('');
        $('#status-filter').val('all');
        $('#sort-filter').val('latest');
        searchTerm = '';
        statusFilter = 'all';
        sortBy = 'latest';
        currentPage = 1;
        loadOrders();
    });

    function loadOrders() {
        $('#loading-spinner').removeClass('d-none');
        $('#orders-container').html('');
        $('#empty-state').addClass('d-none');

        $.ajax({
            url: "{{ route('customer.orders.fetch') }}",
            type: 'GET',
            data: {
                page: currentPage,
                search: searchTerm,
                status: statusFilter,
                sort_by: sortBy
            },
            success: function(response) {
                if (response.success) {
                    renderOrders(response.orders);
                    renderPagination(response.pagination);
                    if (response.orders.length === 0) {
                        $('#empty-state').removeClass('d-none');
                    }
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to load orders', 'error');
            },
            complete: function() {
                $('#loading-spinner').addClass('d-none');
            }
        });
    }

    function renderOrders(orders) {
        const container = $('#orders-container');
        container.html('');

        orders.forEach(order => {
            const card = `
                <div class="order-card">
                    <div class="order-header">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <strong>${order.order_number}</strong>
                            </div>
                            <div class="col-md-2">
                                ${order.items_count} item(s)
                            </div>
                            <div class="col-md-3">
                                <strong>₹${parseFloat(order.final_amount).toFixed(2)}</strong>
                            </div>
                            <div class="col-md-2">
                                ${order.created_at}
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-light btn-sm view-order" data-id="${order.id}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="order-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Status:</strong> ${order.status_badge}
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Payment:</strong> ${order.payment_status_badge}
                            </div>
                        </div>
                        ${['pending', 'processing'].includes(order.status) ? `
                            <div class="mt-3">
                                <button class="btn btn-sm btn-danger cancel-order" data-id="${order.id}">
                                    <i class="fas fa-times me-1"></i> Cancel Order
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function renderPagination(pagination) {
        if (pagination.last_page <= 1) {
            $('#pagination-container').addClass('d-none');
            return;
        }

        const links = $('#pagination-links');
        links.html('');

        for (let i = 1; i <= pagination.last_page; i++) {
            links.append(`
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        $('#pagination-container').removeClass('d-none');
    }

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadOrders();
            $('html, body').animate({ scrollTop: 0 }, 500);
        }
    });

    $(document).on('click', '.view-order', function() {
        const orderId = $(this).data('id');
        
        $.ajax({
            url: "{{ route('customer.orders.show', '') }}/" + orderId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderOrderDetail(response.order);
                    $('#orderModal').modal('show');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to load order details', 'error');
            }
        });
    });

    $(document).on('click', '.cancel-order', function() {
        const orderId = $(this).data('id');
        
        Swal.fire({
            title: 'Cancel Order?',
            text: 'Are you sure you want to cancel this order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('customer.orders.cancel', '') }}/" + orderId,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        Swal.fire('Cancelled!', response.message, 'success');
                        loadOrders();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to cancel order', 'error');
                    }
                });
            }
        });
    });

    function renderOrderDetail(order) {
        let itemsHtml = '';
        order.items.forEach(item => {
            const imageUrl = item.product?.image_path 
                ? '{{ asset("storage") }}/' + item.product.image_path 
                : '/images/default-product.png';
            
            itemsHtml += `
                <div class="product-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="${imageUrl}" class="product-image" alt="${item.product_name}">
                        </div>
                        <div class="col-md-4">
                            <strong>${item.product_name}</strong>
                            <br>
                            <small class="text-muted">SKU: ${item.product_sku}</small>
                        </div>
                        <div class="col-md-2">
                            <strong>Qty:</strong> ${item.quantity}
                        </div>
                        <div class="col-md-2">
                            ₹${parseFloat(item.unit_price).toFixed(2)}
                        </div>
                        <div class="col-md-2 text-end">
                            <strong>₹${parseFloat(item.subtotal).toFixed(2)}</strong>
                        </div>
                    </div>
                </div>
            `;
        });

        const html = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Order Information</h6>
                    <p><strong>Order Number:</strong> ${order.order_number}</p>
                    <p><strong>Order Date:</strong> ${new Date(order.created_at).toLocaleString()}</p>
                    <p><strong>Status:</strong> ${order.status_badge}</p>
                    <p><strong>Payment Status:</strong> ${order.payment_status_badge}</p>
                </div>
                <div class="col-md-6">
                    <h6>Shipping Address</h6>
                    <p>${order.shipping_address}</p>
                </div>
            </div>

            <h6 class="mb-3">Order Items</h6>
            ${itemsHtml}

            <div class="row mt-4">
                <div class="col-md-6 offset-md-6">
                    <table class="table">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-end">₹${parseFloat(order.total_amount).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <td><strong>Tax (18%):</strong></td>
                            <td class="text-end">₹${parseFloat(order.tax_amount).toFixed(2)}</td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>Total:</strong></td>
                            <td class="text-end"><strong>₹${parseFloat(order.final_amount).toFixed(2)}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        `;

        $('#order-detail').html(html);
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
});
</script>
@endsection