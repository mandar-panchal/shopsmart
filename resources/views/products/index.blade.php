@extends('layouts.contentLayoutMaster')
@section('title', 'Product Management')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.6/dist/sweetalert2.min.css">
<style>
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
    .info-card {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
        margin-bottom: 5px;
    }
    .info-value {
        color: #212529;
        font-size: 1rem;
    }
    .product-image-preview {
        max-width: 200px;
        max-height: 200px;
        margin: 10px auto;
        display: block;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 5px;
    }
    .image-upload-preview {
        max-width: 150px;
        max-height: 150px;
        margin-top: 10px;
        display: none;
        border: 2px solid #667eea;
        border-radius: 8px;
        padding: 5px;
    }
    .spinner-overlay {
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
    .spinner-overlay.active {
        display: flex;
    }
    .form-switch .form-check-input {
        cursor: pointer;
        width: 3em;
        height: 1.5em;
    }
    
    /* Product Card Styles */
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
        border-color: #667eea;
    }
    .product-image-container {
        height: 200px;
        overflow: hidden;
        position: relative;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1;
    }
    .product-body {
        padding: 1.25rem;
    }
    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        height: 3rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .product-sku {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-family: monospace;
    }
    .product-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.75rem;
    }
    .product-stock {
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    .product-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .product-actions .btn {
        flex: 1;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    .empty-state-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }
    .filters-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        padding: 1.5rem;
    }
    .pagination-container {
        margin-top: 2rem;
    }
    .loading-spinner {
        text-align: center;
        padding: 3rem;
    }
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 50rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .search-box {
        position: relative;
    }
    .search-box .form-control {
        padding-left: 2.5rem;
    }
    .search-box .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<section id="products-section">
    <!-- Filters Card -->
    <div class="filters-card">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="search-box">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" class="form-control" id="search-input" placeholder="Search products...">
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <select class="form-select" id="status-filter">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <select class="form-select" id="sort-filter">
                    <option value="latest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="price_low">Price (Low to High)</option>
                    <option value="price_high">Price (High to Low)</option>
                    <option value="stock_low">Stock (Low to High)</option>
                    <option value="stock_high">Stock (High to Low)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="fa fa-plus me-1"></i> Add Product
                </button>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div id="products-container">
        <div class="row" id="products-grid">
            <!-- Products will be loaded here -->
        </div>
        
        <!-- Loading Spinner -->
        <div class="loading-spinner d-none" id="loading-spinner">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading products...</p>
        </div>
        
        <!-- Empty State -->
        <div class="empty-state d-none" id="empty-state">
            <div class="empty-state-icon">
                <i class="fa fa-box-open"></i>
            </div>
            <h4>No products found</h4>
            <p class="text-muted">Try adjusting your search or filter to find what you're looking for.</p>
            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#productModal">
                <i class="fa fa-plus me-1"></i> Add First Product
            </button>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-container d-none" id="pagination-container">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="pagination-links">
                    <!-- Pagination links will be loaded here -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-box me-2"></i><span id="modal-title">Add New Product</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="product-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="product_id" name="product_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" id="sku" name="sku" placeholder="e.g., PROD-001" required>
                                <small class="text-muted">Unique product identifier</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="price" name="price" placeholder="0.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="stock" name="stock" placeholder="0" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Image <span class="text-danger" id="image-required">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/webp">
                            <small class="text-muted">Formats: JPG, JPEG, PNG, WEBP (Max 2MB)</small>
                            <div class="invalid-feedback"></div>
                            <img id="image-preview" class="image-upload-preview" alt="Image Preview">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter product description (optional)" maxlength="1000"></textarea>
                            <small class="text-muted"><span id="char-count">0</span>/1000 characters</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="submit-spinner"></span>
                                <i class="fa fa-check me-1"></i> <span id="submit-text">Save Product</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Product Modal -->
    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-eye me-2"></i>Product Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="view-product-content">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="spinner-overlay" id="loading-overlay">
        <div class="text-center">
            <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-light mt-3">Processing...</p>
        </div>
    </div>
</section>
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.6/dist/sweetalert2.all.min.js"></script>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    // Variables
    let currentPage = 1;
    let perPage = 12;
    let searchTerm = '';
    let statusFilter = 'all';
    let sortBy = 'latest';
    let isLoading = false;

    // Initialize
    loadProducts();

    // Search input with debounce
    let searchTimeout;
    $('#search-input').on('input', function() {
        searchTerm = $(this).val();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadProducts();
        }, 500);
    });

    // Status filter
    $('#status-filter').on('change', function() {
        statusFilter = $(this).val();
        currentPage = 1;
        loadProducts();
    });

    // Sort filter
    $('#sort-filter').on('change', function() {
        sortBy = $(this).val();
        currentPage = 1;
        loadProducts();
    });

    // Load products function
    function loadProducts() {
        if (isLoading) return;
        
        isLoading = true;
        $('#loading-spinner').removeClass('d-none');
        $('#products-grid').html('');
        $('#empty-state').addClass('d-none');
        $('#pagination-container').addClass('d-none');

        $.ajax({
            url: "{{ route('products.fetch') }}",
            type: 'GET',
            data: {
                page: currentPage,
                per_page: perPage,
                search: searchTerm,
                status: statusFilter,
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
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load products'
                });
            },
            complete: function() {
                isLoading = false;
                $('#loading-spinner').addClass('d-none');
            }
        });
    }

    // Render products grid
    function renderProducts(products) {
        const grid = $('#products-grid');
        grid.html('');

        products.forEach((product, index) => {
            const card = createProductCard(product);
            grid.append(card);
        });
    }

    // Create product card HTML
    function createProductCard(product) {
        // Get stock status class
        let stockClass = 'bg-success';
        if (product.stock == 0) {
            stockClass = 'bg-danger';
        } else if (product.stock < 10) {
            stockClass = 'bg-warning';
        }

        // Format price
        const price = parseFloat(product.price).toFixed(2);
        
        // Default image if none
        const imageUrl = product.image_url || '{{ asset("images/default-product.png") }}';
        
        return `
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card product-card h-100">
                    <div class="product-image-container position-relative">
                        <img src="${imageUrl}" 
                             class="product-image" 
                             alt="${product.name}"
                             onerror="this.src='{{ asset("images/default-product.png") }}'">
                        <span class="stock-badge status-badge ${stockClass}">
                            ${product.stock} in stock
                        </span>
                    </div>
                    <div class="card-body product-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="product-title mb-0">${product.name}</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input status-toggle" 
                                       type="checkbox" 
                                       ${product.is_active ? 'checked' : ''}
                                       data-id="${product.id}">
                            </div>
                        </div>
                        <div class="product-sku mb-2">SKU: ${product.sku}</div>
                        <div class="product-price">₹${price}</div>
                        <div class="product-stock">
                            <span class="badge ${product.is_active ? 'bg-success' : 'bg-danger'}">
                                ${product.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-outline-primary view-product-btn" data-id="${product.id}">
                                <i class="fa fa-eye me-1"></i> View
                            </button>
                            <button class="btn btn-outline-warning edit-product-btn" data-id="${product.id}">
                                <i class="fa fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-outline-danger delete-product-btn" data-id="${product.id}">
                                <i class="fa fa-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Render pagination
    function renderPagination(pagination) {
        if (pagination.last_page <= 1) {
            return;
        }

        const container = $('#pagination-container');
        const links = $('#pagination-links');
        links.html('');

        // Previous button
        const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
        links.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="fa fa-chevron-left"></i>
                </a>
            </li>
        `);

        // Page numbers
        const totalPages = pagination.last_page;
        const currentPage = pagination.current_page;
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (currentPage <= 3) {
            endPage = Math.min(5, totalPages);
        }
        if (currentPage >= totalPages - 2) {
            startPage = Math.max(1, totalPages - 4);
        }

        if (startPage > 1) {
            links.append('<li class="page-item disabled"><a class="page-link">...</a></li>');
        }

        for (let i = startPage; i <= endPage; i++) {
            const active = i === currentPage ? 'active' : '';
            links.append(`
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        if (endPage < totalPages) {
            links.append('<li class="page-item disabled"><a class="page-link">...</a></li>');
        }

        // Next button
        const nextDisabled = pagination.current_page === pagination.last_page ? 'disabled' : '';
        links.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    <i class="fa fa-chevron-right"></i>
                </a>
            </li>
        `);

        container.removeClass('d-none');
    }

    // Pagination click handler
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        if ($(this).parent().hasClass('disabled')) return;
        
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadProducts();
            $('html, body').animate({ scrollTop: $('#products-container').offset().top - 100 }, 500);
        }
    });

    // Character counter for description
    $('#description').on('input', function() {
        var count = $(this).val().length;
        $('#char-count').text(count);
    });

    // Image preview
    $('#image').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });

    // Reset modal form
    function resetModal() {
        $('#product-form')[0].reset();
        $('#product_id').val('');
        $('#modal-title').text('Add New Product');
        $('#submit-text').text('Save Product');
        $('#image').prop('required', true);
        $('#image-required').show();
        $('#image-preview').hide();
        $('#char-count').text('0');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    // Modal hidden event
    $('#productModal').on('hidden.bs.modal', function () {
        resetModal();
    });

    // Product Form Submit
    $('#product-form').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        var formData = new FormData(this);
        var url = $('#product_id').val() 
            ? "{{ route('products.update') }}" 
            : "{{ route('products.store') }}";

        $('#submit-btn').prop('disabled', true);
        $('#submit-spinner').removeClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#productModal').modal('hide');
                loadProducts();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        var input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(value[0]);
                    });
                } else {
                    var errorMsg = xhr.responseJSON?.message || 'An error occurred';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                }
            },
            complete: function() {
                $('#submit-btn').prop('disabled', false);
                $('#submit-spinner').addClass('d-none');
            }
        });
    });

    // Edit Product Button
    $(document).on('click', '.edit-product-btn', function() {
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
        
        var productId = $(this).data('id');
        
        $.ajax({
            url: '/products/edit/' + productId,
            type: 'GET',
            success: function(data) {
                $('#product_id').val(data.id);
                $('#name').val(data.name);
                $('#sku').val(data.sku);
                $('#price').val(data.price);
                $('#stock').val(data.stock);
                $('#is_active').prop('checked', data.is_active);
                $('#description').val(data.description || '');
                $('#char-count').text(data.description?.length || 0);
                
                if (data.image_url) {
                    $('#image-preview').attr('src', data.image_url).show();
                }
                
                $('#modal-title').text('Edit Product');
                $('#submit-text').text('Update Product');
                $('#image').prop('required', false);
                $('#image-required').hide();
                
                $('#productModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to load product details'
                });
            },
            complete: function() {
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });

    // View Product Button
    $(document).on('click', '.view-product-btn', function() {
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
        
        var productId = $(this).data('id');
        
        $.ajax({
            url: '/products/view/' + productId,
            type: 'GET',
            success: function(data) {
                var content = `
                    <div class="row">
                        ${data.image_url ? `
                            <div class="col-12 text-center mb-3">
                                <img src="${data.image_url}" alt="${data.name}" class="product-image-preview">
                            </div>
                        ` : ''}
                        
                        <div class="col-md-6 mb-2">
                            <div class="info-card">
                                <div class="info-label">Product Name</div>
                                <div class="info-value">${data.name}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-2">
                            <div class="info-card">
                                <div class="info-label">SKU</div>
                                <div class="info-value">${data.sku}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-2">
                            <div class="info-card">
                                <div class="info-label">Price</div>
                                <div class="info-value">₹${parseFloat(data.price).toFixed(2)}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-2">
                            <div class="info-card">
                                <div class="info-label">Stock</div>
                                <div class="info-value">${data.stock} units</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-2">
                            <div class="info-card">
                                <div class="info-label">Status</div>
                                <div class="info-value">
                                    <span class="badge ${data.is_active ? 'bg-success' : 'bg-danger'}">
                                        ${data.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        ${data.description ? `
                            <div class="col-12 mb-2">
                                <div class="info-card">
                                    <div class="info-label">Description</div>
                                    <div class="info-value">${data.description}</div>
                                </div>
                            </div>
                        ` : ''}
                        
                        <div class="col-md-6 mb-2">
                            <div class="info-card">
                                <div class="info-label">Created At</div>
                                <div class="info-value">${new Date(data.created_at).toLocaleString()}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-2">
                            <div class="info-card">
                                <div class="info-label">Last Updated</div>
                                <div class="info-value">${new Date(data.updated_at).toLocaleString()}</div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#view-product-content').html(content);
                $('#viewProductModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to load product details'
                });
            },
            complete: function() {
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });

    // Delete Product Button
    $(document).on('click', '.delete-product-btn', function() {
        var productId = $(this).data('id');
        var $btn = $(this);
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This will soft delete the product!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fa fa-trash me-1"></i> Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var originalHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
                
                $.ajax({
                    url: '/products/delete/' + productId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        loadProducts();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to delete product'
                        });
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }
        });
    });

    // Toggle Product Status
    $(document).on('change', '.status-toggle', function() {
        var $toggle = $(this);
        var productId = $toggle.data('id');
        var originalState = !$toggle.is(':checked');
        
        $.ajax({
            url: '/products/toggle-status/' + productId,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                loadProducts(); // Reload to update UI
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: 'top-end'
                });
            },
            error: function(xhr) {
                // Revert toggle on error
                $toggle.prop('checked', originalState);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to update status',
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    });
});
</script>
@endsection