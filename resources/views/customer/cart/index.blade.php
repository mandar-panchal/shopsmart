@extends('layouts.app')
@section('title', 'Shopping Cart')

@section('styles')
<style>
    .cart-card {
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }
    .cart-item {
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem 0;
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .product-image {
        width: 100px;
        height: 100px;
        object-fit: contain;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 10px;
    }
    .quantity-control {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .quantity-control button {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .quantity-control button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    .quantity-control input {
        width: 60px;
        text-align: center;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.5rem;
    }
    .summary-card {
        border-radius: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        position: sticky;
        top: 100px;
    }
    .summary-card .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .summary-card .summary-row:last-child {
        border-bottom: none;
        font-size: 1.5rem;
        font-weight: 700;
        padding-top: 1rem;
    }
    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-cart i {
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
            <h2><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h2>
            <p class="text-muted">Review and manage your cart items</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('customer.products') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Continue Shopping
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <div class="card cart-card">
                <div class="card-body" id="cart-items-container">
                    @if($cartItems->isEmpty())
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <h4 class="mt-3">Your cart is empty</h4>
                            <p class="text-muted">Add some products to get started</p>
                            <a href="{{ route('customer.products') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-shopping-bag me-1"></i> Start Shopping
                            </a>
                        </div>
                    @else
                        @foreach($cartItems as $item)
                            <div class="cart-item" data-item-id="{{ $item->id }}">
                                <div class="row align-items-center">
                                    <!-- Product Image -->
                                    <div class="col-md-2 text-center">
                                        <img src="{{ $item->product->image_path ? asset('storage/' . $item->product->image_path) : '/images/default-product.png' }}" 
                                             class="product-image" 
                                             alt="{{ $item->product->name }}"
                                             onerror="this.src='/images/default-product.png'">
                                    </div>

                                    <!-- Product Details -->
                                    <div class="col-md-4">
                                        <h5 class="mb-1">{{ $item->product->name }}</h5>
                                        <p class="text-muted small mb-1">SKU: {{ $item->product->sku }}</p>
                                        <p class="text-success small mb-0">
                                            <i class="fas fa-check-circle"></i> In Stock ({{ $item->product->stock }})
                                        </p>
                                    </div>

                                    <!-- Price -->
                                    <div class="col-md-2">
                                        <strong class="text-primary">₹{{ number_format($item->product->price, 2) }}</strong>
                                    </div>

                                    <!-- Quantity Control -->
                                    <div class="col-md-2">
                                        <div class="quantity-control">
                                            <button class="qty-decrease" data-id="{{ $item->id }}">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control qty-input" 
                                                   value="{{ $item->quantity }}" 
                                                   min="1" 
                                                   max="{{ $item->product->stock }}"
                                                   data-id="{{ $item->id }}"
                                                   readonly>
                                            <button class="qty-increase" data-id="{{ $item->id }}" data-max="{{ $item->product->stock }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Subtotal & Remove -->
                                    <div class="col-md-2 text-end">
                                        <strong class="d-block mb-2 item-subtotal">₹{{ number_format($item->subtotal, 2) }}</strong>
                                        <button class="btn btn-sm btn-outline-danger remove-item" data-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Clear Cart -->
                        <div class="text-end mt-3">
                            <button class="btn btn-outline-danger" id="clear-cart">
                                <i class="fas fa-trash-alt me-1"></i> Clear Cart
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        @if(!$cartItems->isEmpty())
        <div class="col-lg-4">
            <div class="summary-card">
                <h5 class="mb-4"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                
                <div class="summary-row">
                    <span>Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                    <strong id="cart-subtotal">₹{{ number_format($total, 2) }}</strong>
                </div>
                
                <div class="summary-row">
                    <span>Tax (18%)</span>
                    <strong id="cart-tax">₹{{ number_format($total * 0.18, 2) }}</strong>
                </div>
                
                <div class="summary-row">
                    <span>Shipping</span>
                    <strong>FREE</strong>
                </div>
                
                <div class="summary-row">
                    <span>Total</span>
                    <strong id="cart-total">₹{{ number_format($total * 1.18, 2) }}</strong>
                </div>

                <a href="{{ route('customer.checkout') }}" class="btn btn-light w-100 mt-4 py-3">
                    <i class="fas fa-lock me-2"></i> Proceed to Checkout
                </a>

                <div class="text-center mt-3">
                    <small><i class="fas fa-shield-alt me-1"></i> Secure Checkout</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    
    // Increase quantity
    $(document).on('click', '.qty-increase', function() {
        const $btn = $(this);
        const itemId = $btn.data('id');
        const max = parseInt($btn.data('max'));
        const $input = $(`.qty-input[data-id="${itemId}"]`);
        const currentQty = parseInt($input.val());
        
        if (currentQty < max) {
            updateQuantity(itemId, currentQty + 1);
        } else {
            Swal.fire('Error!', `Maximum available quantity is ${max}`, 'error');
        }
    });

    // Decrease quantity
    $(document).on('click', '.qty-decrease', function() {
        const itemId = $(this).data('id');
        const $input = $(`.qty-input[data-id="${itemId}"]`);
        const currentQty = parseInt($input.val());
        
        if (currentQty > 1) {
            updateQuantity(itemId, currentQty - 1);
        }
    });

    // Update quantity
    function updateQuantity(itemId, quantity) {
        showLoader();
        
        $.ajax({
            url: "{{ route('customer.cart.update', '') }}/" + itemId,
            type: 'PUT',
            data: { quantity: quantity },
            success: function(response) {
                if (response.success) {
                    // Update UI
                    $(`.qty-input[data-id="${itemId}"]`).val(quantity);
                    
                    // Update item subtotal
                    const $item = $(`.cart-item[data-item-id="${itemId}"]`);
                    $item.find('.item-subtotal').text('₹' + parseFloat(response.subtotal).toFixed(2));
                    
                    // Recalculate totals
                    updateCartTotals();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to update cart', 'error');
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    // Remove item
    $(document).on('click', '.remove-item', function() {
        const itemId = $(this).data('id');
        
        Swal.fire({
            title: 'Remove Item?',
            text: 'Are you sure you want to remove this item from cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                removeItem(itemId);
            }
        });
    });

    function removeItem(itemId) {
        showLoader();
        
        $.ajax({
            url: "{{ route('customer.cart.remove', '') }}/" + itemId,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    // Remove item from DOM
                    $(`.cart-item[data-item-id="${itemId}"]`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if cart is empty
                        if ($('.cart-item').length === 0) {
                            location.reload();
                        } else {
                            updateCartTotals();
                        }
                    });
                    
                    // Update cart badge
                    $('#cart-badge').text(response.cart_count);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Removed!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
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

    // Clear cart
    $('#clear-cart').on('click', function() {
        Swal.fire({
            title: 'Clear Cart?',
            text: 'Are you sure you want to remove all items from cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                clearCart();
            }
        });
    });

    function clearCart() {
        showLoader();
        
        $.ajax({
            url: "{{ route('customer.cart.clear') }}",
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cart Cleared!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to clear cart', 'error');
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    // Update cart totals
    function updateCartTotals() {
        let subtotal = 0;
        
        $('.cart-item').each(function() {
            const itemSubtotal = parseFloat($(this).find('.item-subtotal').text().replace('₹', '').replace(',', ''));
            subtotal += itemSubtotal;
        });
        
        const tax = subtotal * 0.18;
        const total = subtotal + tax;
        
        $('#cart-subtotal').text('₹' + subtotal.toFixed(2));
        $('#cart-tax').text('₹' + tax.toFixed(2));
        $('#cart-total').text('₹' + total.toFixed(2));
    }
});
</script>
@endsection