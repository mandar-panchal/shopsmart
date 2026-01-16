@extends('layouts.app')
@section('title', 'Shop Products')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .product-card {
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .product-image-container {
        height: 250px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
        overflow: hidden;
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 15px;
    }
    .wishlist-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        z-index: 1;
    }
    .wishlist-btn.active {
        background: #dc3545;
        color: white;
    }
    .stock-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 1;
    }
    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        height: 2.5rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0d6efd;
    }
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .loading-overlay.active {
        display: flex;
    }
    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-shopping-bag me-2"></i>Shop Products</h2>
            <p class="text-muted">Browse our collection of quality products</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('customer.cart') }}" class="btn btn-outline-primary me-2 position-relative">
                <i class="fas fa-shopping-cart"></i> Cart
                <span class="cart-badge" id="cart-badge">0</span>
            </a>
            <a href="{{ route('customer.wishlist') }}" class="btn btn-outline-danger">
                <i class="fas fa-heart"></i> Wishlist
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search-input" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" id="min-price" placeholder="Min Price">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" id="max-price" placeholder="Max Price">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="sort-filter">
                        <option value="newest">Newest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="name_asc">Name: A-Z</option>
                        <option value="name_desc">Name: Z-A</option>
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

    <!-- Products Grid -->
    <div id="products-container">
        <div class="row" id="products-grid">
            <!-- Products will be loaded here -->
        </div>
        
        <div class="text-center py-5 d-none" id="loading-spinner">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
            <p class="mt-2">Loading products...</p>
        </div>
        
        <div class="text-center py-5 d-none" id="empty-state">
            <i class="fas fa-box-open" style="font-size: 4rem; color: #dee2e6;"></i>
            <h4 class="mt-3">No products found</h4>
            <p class="text-muted">Try adjusting your filters</p>
        </div>
        
        <!-- Pagination -->
        <nav class="mt-4 d-none" id="pagination-container">
            <ul class="pagination justify-content-center" id="pagination-links"></ul>
        </nav>
    </div>
</div>

<!-- Product Detail Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-box me-2"></i>Product Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="product-detail">
                <!-- Product details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay">
    <div class="text-center">
        <div class="spinner-border text-light" style="width: 3rem; height: 3rem;"></div>
        <p class="text-light mt-3">Processing...</p>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let currentPage = 1;
    let searchTerm = '';
    let minPrice = '';
    let maxPrice = '';
    let sortBy = 'newest';

    loadProducts();
    updateCartBadge();

    // Search with debounce
    let searchTimeout;
    $('#search-input').on('input', function() {
        searchTerm = $(this).val();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadProducts();
        }, 500);
    });

    // Price filters
    $('#min-price, #max-price').on('change', function() {
        minPrice = $('#min-price').val();
        maxPrice = $('#max-price').val();
        currentPage = 1;
        loadProducts();
    });

    $('#sort-filter').on('change', function() {
        sortBy = $(this).val();
        currentPage = 1;
        loadProducts();
    });

    $('#reset-filters').on('click', function() {
        $('#search-input').val('');
        $('#min-price').val('');
        $('#max-price').val('');
        $('#sort-filter').val('newest');
        searchTerm = '';
        minPrice = '';
        maxPrice = '';
        sortBy = 'newest';
        currentPage = 1;
        loadProducts();
    });

    function loadProducts() {
        $('#loading-spinner').removeClass('d-none');
        $('#products-grid').html('');
        $('#empty-state').addClass('d-none');

        $.ajax({
            url: "{{ route('customer.products.fetch') }}",
            type: 'GET',
            data: {
                page: currentPage,
                search: searchTerm,
                min_price: minPrice,
                max_price: maxPrice,
                sort_by: sortBy
            },
            success: function(response) {
                if (response.success) {
                    renderProducts(response.products);
                    renderPagination(response.pagination);
                    if (response.products.length === 0) {
                        $('#empty-state').removeClass('d-none');
                    }
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to load products', 'error');
            },
            complete: function() {
                $('#loading-spinner').addClass('d-none');
            }
        });
    }

    function renderProducts(products) {
        const grid = $('#products-grid');
        grid.html('');

        products.forEach(product => {
            const card = `
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card product-card">
                        <div class="product-image-container">
                            <img src="${product.image_url || '/images/default-product.png'}" 
                                 class="product-image" alt="${product.name}">
                            <button class="wishlist-btn ${product.in_wishlist ? 'active' : ''}" 
                                    data-id="${product.id}">
                                <i class="fas fa-heart"></i>
                            </button>
                            
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">${product.name}</h5>
                            <p class="text-muted small mb-2">SKU: ${product.sku}</p>
                            <div class="product-price mb-3">${product.formatted_price}</div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-sm add-to-cart" 
                                        data-id="${product.id}" 
                                        ${product.stock === 0 ? 'disabled' : ''}>
                                    <i class="fas fa-cart-plus me-1"></i> 
                                    ${product.cart_quantity > 0 ? 'In Cart (' + product.cart_quantity + ')' : 'Add to Cart'}
                                </button>
                               
                            </div>
                        </div>
                    </div>
                </div>
            `;
            grid.append(card);
        });
    }

    function renderPagination(pagination) {
        if (pagination.last_page <= 1) {
            $('#pagination-container').addClass('d-none');
            return;
        }

        const links = $('#pagination-links');
        links.html('');

        links.append(`
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
            </li>
        `);

        for (let i = 1; i <= pagination.last_page; i++) {
            links.append(`
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        links.append(`
            <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
            </li>
        `);

        $('#pagination-container').removeClass('d-none');
    }

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled')) {
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                loadProducts();
                $('html, body').animate({ scrollTop: 0 }, 500);
            }
        }
    });

    // Add to cart
    $(document).on('click', '.add-to-cart', function() {
        const $btn = $(this);
        const productId = $btn.data('id');
        const originalHtml = $btn.html();
        
        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        $.ajax({
            url: "{{ route('customer.cart.add') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { product_id: productId, quantity: 1 },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                updateCartBadge();
                loadProducts();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to add to cart', 'error');
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });

    // Wishlist toggle
    $(document).on('click', '.wishlist-btn', function() {
        const $btn = $(this);
        const productId = $btn.data('id');
        const isInWishlist = $btn.hasClass('active');

        $.ajax({
            url: isInWishlist ? "{{ route('customer.wishlist.remove', '') }}/" + productId : "{{ route('customer.wishlist.add') }}",
            type: isInWishlist ? 'DELETE' : 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { product_id: productId },
            success: function(response) {
                $btn.toggleClass('active');
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            },
            error: function() {
                Swal.fire('Error!', 'Failed to update wishlist', 'error');
            }
        });
    });

    function updateCartBadge() {
        $.get("{{ route('customer.cart.count') }}", function(response) {
            $('#cart-badge').text(response.count);
        });
    }
});
</script>
@endsection