@extends('layouts.app')
@section('title', 'Checkout')

@section('styles')
<style>
    .checkout-step {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .step-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 1rem;
    }
    .order-summary-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        position: sticky;
        top: 100px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .summary-total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        margin-top: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-shopping-bag me-2"></i>Checkout</h2>
            <p class="text-muted">Complete your order</p>
        </div>
    </div>

    <form id="checkout-form">
        <div class="row">
            <!-- Checkout Steps -->
            <div class="col-lg-8">
                <!-- Step 1: Shipping Address -->
                <div class="checkout-step">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <h5 class="mb-0">Shipping Address</h5>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address_line1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" name="address_line2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pincode" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="country" value="India" required>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Payment Method -->
                <div class="checkout-step">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" value="online" id="online">
                                <label class="form-check-label" for="online">
                                    <i class="fas fa-credit-card me-2"></i>Online Payment (Card/UPI/Net Banking)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Order Notes -->
                <div class="checkout-step">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <h5 class="mb-0">Additional Notes (Optional)</h5>
                    </div>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Any special instructions for delivery..."></textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary-card">
                    <h5 class="mb-4"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                    
                    <div id="order-items">
                        <!-- Items will be loaded here -->
                    </div>

                    <div class="summary-item">
                        <span>Subtotal</span>
                        <strong id="subtotal">₹0.00</strong>
                    </div>
                    <div class="summary-item">
                        <span>Tax (18%)</span>
                        <strong id="tax">₹0.00</strong>
                    </div>
                    <div class="summary-item">
                        <span>Shipping</span>
                        <strong class="text-success">FREE</strong>
                    </div>

                    <div class="summary-total">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">Total</span>
                            <strong class="h4 mb-0" id="total">₹0.00</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3 py-3" id="place-order-btn">
                        <i class="fas fa-lock me-2"></i> Place Order
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i> 
                            Your payment information is secure
                        </small>
                    </div>

                    <a href="{{ route('customer.cart') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    loadOrderSummary();

    // Load order summary
    function loadOrderSummary() {
        showLoader();
        
        $.ajax({
            url: "{{ route('customer.cart') }}", // You'll need to create an API endpoint
            type: 'GET',
            success: function(response) {
                // This is a placeholder - you'll need to create a proper API endpoint
                // For now, we'll use dummy data
                renderOrderSummary();
            },
            error: function() {
                Swal.fire('Error!', 'Failed to load order summary', 'error');
            },
            complete: function() {
                hideLoader();
            }
        });
    }

    function renderOrderSummary() {
        // Placeholder function - implement based on your cart data
        const subtotal = 1000;
        const tax = subtotal * 0.18;
        const total = subtotal + tax;

        $('#subtotal').text('₹' + subtotal.toFixed(2));
        $('#tax').text('₹' + tax.toFixed(2));
        $('#total').text('₹' + total.toFixed(2));
    }

    // Submit checkout form
    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        // Build shipping address
        const shippingAddress = `${$('[name="full_name"]').val()}, ${$('[name="phone"]').val()}, ${$('[name="address_line1"]').val()}, ${$('[name="address_line2"]').val()}, ${$('[name="city"]').val()}, ${$('[name="state"]').val()} - ${$('[name="pincode"]').val()}, ${$('[name="country"]').val()}`;
        
        const orderData = {
            shipping_address: shippingAddress,
            billing_address: shippingAddress,
            payment_method: $('[name="payment_method"]:checked').val(),
            notes: $('[name="notes"]').val()
        };

        placeOrder(orderData);
    });

    function placeOrder(orderData) {
        showLoader();
        $('#place-order-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
        
        $.ajax({
            url: "{{ route('customer.orders.create') }}",
            type: 'POST',
            data: orderData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed!',
                        text: 'Your order has been placed successfully',
                        confirmButtonText: 'View Order'
                    }).then(() => {
                        window.location.href = "{{ route('customer.orders') }}";
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to place order', 'error');
                $('#place-order-btn').prop('disabled', false).html('<i class="fas fa-lock me-2"></i> Place Order');
            },
            complete: function() {
                hideLoader();
            }
        });
    }
});
</script>
@endsection