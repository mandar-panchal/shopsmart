@extends('layouts.app')
@section('title', 'My Wishlist')

@section('styles')
<style>
    .wishlist-card {
        border-radius: 15px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
        height: 100%;
    }
    .wishlist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .product-image-container {
        height: 200px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
        overflow: hidden;
        border-radius: 15px 15px 0 0;
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 15px;
    }
    .remove-btn {
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
        color: #dc3545;
        transition: all 0.3s ease;
    }
    .remove-btn:hover {
        background: #dc3545;
        color: white;
        transform: scale(1.1);
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
        font-size: 1.3rem;
        font-weight: 700;
        color: #0d6efd;
    }
    .empty-wishlist {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-wishlist i {
        font-size: 5rem;
        color: #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-heart me-2 text-danger"></i>My Wishlist</h2>
            <p class="text-muted">Save your favorite products for later</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('customer.products') }}" class="btn btn-outline-primary">
                <i class="fas fa-shopping-bag me-1"></i> Continue Shopping
            </a>
        </div>
    </div>

    @if($wishlistItems->isEmpty())
        <!-- Empty State -->
        <div class="card">
            <div class="card-body">
                <div class="empty-wishlist">
                    <i class="fas fa-heart-broken"></i>
                    <h4 class="mt-3">Your wishlist is empty</h4>
                    <p class="text-muted">Add products you love to your wishlist</p>
                    <a href="{{ route('customer.products') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-shopping-bag me-1"></i> Explore Products
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Wishlist Stats -->
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            You have <strong>{{ $wishlistItems->count() }}</strong> {{ Str::plural('item', $wishlistItems->count()) }} in your wishlist
        </div>

        <!-- Wishlist Items Grid -->
        <div class="row" id="wishlist-grid">
            @foreach($wishlistItems as $item)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4 wishlist-item" data-item-id="{{ $item->id }}">
                    <div class="card wishlist-card">
                        <div class="product-image-container">
                            <img src="{{ $item->product->image_path ? asset('storage/' . $item->product->image_path) : '/images/default-product.png' }}" 
                                 class="product-image" 
                                 alt="{{ $item->product->name }}"
                                 onerror="this.src='/images/default-product.png'">
                            
                            <button class="remove-btn remove-from-wishlist" data-id="{{ $item->product_id }}" title="Remove from wishlist">
                                <i class="fas fa-times"></i>
                            </button>
                            
                            <span class="stock-badge badge bg-{{ $item->product->stock > 10 ? 'success' : ($item->product->stock > 0 ? 'warning' : 'danger') }}">
                                @if($item->product->stock > 0)
                                    {{ $item->product->stock }} in stock
                                @else
                                    Out of stock
                                @endif
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="product-title">{{ $item->product->name }}</h5>
                            <p class="text-muted small mb-2">SKU: {{ $item->product->sku }}</p>
                            <div class="product-price mb-3">₹{{ number_format($item->product->price, 2) }}</div>
                            
                            @if($item->product->stock > 0 && $item->product->is_active)
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-sm add-to-cart" data-id="{{ $item->product_id }}">
                                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                    </button>
                                    <a href="{{ route('customer.products') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            @else
                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                    <i class="fas fa-ban me-1"></i> Out of Stock
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bulk Actions -->
        <div class="text-center mt-4">
            <button class="btn btn-outline-danger" id="clear-wishlist">
                <i class="fas fa-trash-alt me-1"></i> Clear Wishlist
            </button>
            <button class="btn btn-primary" id="add-all-to-cart">
                <i class="fas fa-cart-plus me-1"></i> Add All to Cart
            </button>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    
    // Remove from wishlist
    $(document).on('click', '.remove-from-wishlist', function() {
        const $btn = $(this);
        const productId = $btn.data('id');
        const $item = $btn.closest('.wishlist-item');
        
        Swal.fire({
            title: 'Remove from Wishlist?',
            text: 'Are you sure you want to remove this item?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                removeFromWishlist(productId, $item);
            }
        });
    });

    function removeFromWishlist(productId, $item) {
        showLoader();
        
        $.ajax({
            url: "{{ route('customer.wishlist.remove', '') }}/" + productId,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if wishlist is empty
                        if ($('.wishlist-item').length === 0) {
                            location.reload();
                        }
                    });
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Removed!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to remove item', 'error');
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    // Add to cart from wishlist
    $(document).on('click', '.add-to-cart', function() {
        const $btn = $(this);
        const productId = $btn.data('id');
        const originalHtml = $btn.html();
        
        $btn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        $.ajax({
            url: "{{ route('customer.cart.add') }}",
            type: 'POST',
            data: { 
                product_id: productId, 
                quantity: 1 
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Cart!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Update cart badge
                    $('#cart-badge').text(response.cart_count);
                    
                    // Update button
                    $btn.html('<i class="fas fa-check me-1"></i> In Cart').removeClass('btn-primary').addClass('btn-success');
                    
                    setTimeout(() => {
                        $btn.html(originalHtml).removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
                    }, 2000);
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to add to cart', 'error');
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });

    // Clear entire wishlist
    $('#clear-wishlist').on('click', function() {
        Swal.fire({
            title: 'Clear Wishlist?',
            text: 'Are you sure you want to remove all items from your wishlist?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                clearWishlist();
            }
        });
    });

    function clearWishlist() {
        showLoader();
        
        let itemsToRemove = [];
        $('.wishlist-item').each(function() {
            const productId = $(this).find('.remove-from-wishlist').data('id');
            itemsToRemove.push(productId);
        });

        let promises = itemsToRemove.map(productId => {
            return $.ajax({
                url: "{{ route('customer.wishlist.remove', '') }}/" + productId,
                type: 'DELETE'
            });
        });

        Promise.all(promises)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Wishlist Cleared!',
                    text: 'All items have been removed from your wishlist',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            })
            .catch(() => {
                Swal.fire('Error!', 'Failed to clear wishlist', 'error');
            })
            .finally(() => {
                hideLoader();
            });
    }

    // Add all to cart
    $('#add-all-to-cart').on('click', function() {
        Swal.fire({
            title: 'Add All to Cart?',
            text: 'Do you want to add all available items to your cart?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'Yes, add all!'
        }).then((result) => {
            if (result.isConfirmed) {
                addAllToCart();
            }
        });
    });

    function addAllToCart() {
        showLoader();
        
        let itemsToAdd = [];
        $('.add-to-cart:not(:disabled)').each(function() {
            itemsToAdd.push($(this).data('id'));
        });

        if (itemsToAdd.length === 0) {
            hideLoader();
            Swal.fire('Info', 'No items available to add to cart', 'info');
            return;
        }

        let promises = itemsToAdd.map(productId => {
            return $.ajax({
                url: "{{ route('customer.cart.add') }}",
                type: 'POST',
                data: { 
                    product_id: productId, 
                    quantity: 1 
                }
            });
        });

        Promise.all(promises)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: `${itemsToAdd.length} items added to cart`,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Update cart badge
                $.get("{{ route('customer.cart.count') }}", function(response) {
                    if (response.success) {
                        $('#cart-badge').text(response.count);
                    }
                });
            })
            .catch(() => {
                Swal.fire('Error!', 'Some items could not be added to cart', 'error');
            })
            .finally(() => {
                hideLoader();
            });
    }
});
</script>
@endsection