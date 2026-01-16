<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SHOP SMART') - Shop Quality Products</title>
    
    <link rel="icon" href="{{ asset('images/logo/favicon-32x32.png') }}">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #38ef7d;
            --danger-color: #f5576c;
            --dark-color: #2d3748;
            --light-bg: #f8f9fa;
        }

        body {
            background: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-color);
        }

        /* Navbar Styling */
        .navbar {
            background: #ffffff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-nav .nav-link {
            color: var(--dark-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .search-bar {
            max-width: 500px;
            position: relative;
        }

        .search-bar input {
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .search-bar .btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
        }

        .icon-badge {
            position: relative;
            display: inline-block;
        }

        .icon-badge .badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: var(--danger-color);
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 25px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Main Content */
        main {
            min-height: calc(100vh - 200px);
            padding: 2rem 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: #cbd5e0;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        footer h6 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        footer a {
            color: #cbd5e0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: white;
        }

        footer .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        footer .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        /* Loader */
        .loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .loader-overlay.active {
            display: flex;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 4px solid #e0e0e0;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.7rem 1.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            color: var(--primary-color);
        }

        /* Alert Messages */
        .alert {
            border-radius: 12px;
            border: none;
        }

        @media (max-width: 991px) {
            .search-bar {
                margin: 1rem 0;
            }
        }
    </style>

    @yield('styles')
</head>
<body>

    <!-- Loader -->
    <div id="loader" class="loader-overlay">
        <div class="text-center">
            <div class="loader"></div>
            <p class="mt-3 fw-bold text-dark">Loading...</p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-shopping-bag me-2"></i>SHOP SMART
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar -->
                <form class="search-bar mx-auto my-3 my-lg-0" action="{{ route('customer.products') }}" method="GET">
                    <div class="input-group">
                        <input type="search" class="form-control" name="search" placeholder="Search products...">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Right Navigation -->
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('customer/products*') ? 'active' : '' }}" href="{{ route('customer.products') }}">
                            <i class="fas fa-store me-1"></i> Shop
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link icon-badge {{ Request::is('customer/wishlist*') ? 'active' : '' }}" href="{{ route('customer.wishlist') }}">
                            <i class="fas fa-heart"></i>
                            <span class="badge" id="wishlist-badge">0</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link icon-badge {{ Request::is('customer/cart*') ? 'active' : '' }}" href="{{ route('customer.cart') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge" id="cart-badge">0</span>
                        </a>
                    </li>

                    @auth('customer')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ auth('customer')->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.orders') }}">
                                        <i class="fas fa-box me-2"></i> My Orders
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('customer.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary" href="{{ route('customer.login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h6><i class="fas fa-shopping-bag me-2"></i>Shop Smart</h6>
                    <p>Your trusted destination for quality products at unbeatable prices. Shop with confidence!</p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('customer.products') }}"><i class="fas fa-chevron-right me-2"></i>Shop</a></li>
                        <li class="mb-2"><a href="{{ route('customer.cart') }}"><i class="fas fa-chevron-right me-2"></i>Cart</a></li>
                        <li class="mb-2"><a href="{{ route('customer.orders') }}"><i class="fas fa-chevron-right me-2"></i>Orders</a></li>
                        <li class="mb-2"><a href="{{ route('customer.wishlist') }}"><i class="fas fa-chevron-right me-2"></i>Wishlist</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4 mb-4 mb-lg-0">
                    <h6>Customer Service</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Help Center</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Track Order</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Returns</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i>Shipping Info</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h6>Contact Us</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:support@eeestore.com">support@shopsmart.com</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:+911234567890">+91 123-456-7890</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Mumbai, Maharashtra, India
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; {{ date('Y') }} Shop Smart. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="me-3">Privacy Policy</a>
                    <a href="#" class="me-3">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global loader functions
        function showLoader() {
            $('#loader').addClass('active');
        }

        function hideLoader() {
            $('#loader').removeClass('active');
        }

        // Update cart and wishlist badges
        function updateBadges() {
            @auth('customer')
            $.ajax({
                url: "{{ route('customer.cart.count') }}",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#cart-badge').text(response.count);
                    }
                }
            });
            @endauth
        }

        $(document).ready(function() {
            // Auto-dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Update badges on page load
            updateBadges();

            // Setup AJAX defaults
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>