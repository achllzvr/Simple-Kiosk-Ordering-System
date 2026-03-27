<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KFC Ordering System')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/KFC_logo_full_icon.png') }}">
    <link href="https://fonts.cdnfonts.com/css/kfcclassicscript" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/helmet" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --kfc-red: #C41230;
            --kfc-dark: #261C02;
            --kfc-cream: #F5D4B7;
            --kfc-light: #FFF1E2;
            --kfc-black: #1A1A1A;
            --font-kfc-script: 'KFCClassicScript', 'Brush Script MT', cursive;
            --font-helmet: 'Helmet', 'Arial Black', sans-serif;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: var(--kfc-light);
            color: var(--kfc-dark);
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px 0;
        }

        main .container {
            padding: 0 20px;
        }

        .kiosk-mode main {
            padding: 40px 0 20px;
        }

        .kiosk-mode main .container {
            padding: 0 30px;
        }

        .navbar {
            background-color: var(--kfc-red);
            box-shadow: 0 4px 14px rgba(196, 18, 48, 0.25);
        }

        .navbar-brand {
            font-family: var(--font-kfc-script);
            font-weight: 400;
            font-size: 1.5rem;
            letter-spacing: 2px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #fff !important;
        }

        .navbar-brand-icon,
        .footer-brand-icon {
            object-fit: contain;
            background-color: #fff;
            border-radius: 12px;
            border: 2px solid var(--kfc-black);
        }

        .navbar-brand-icon {
            width: 38px;
            height: 38px;
            padding: 3px;
        }

        .footer-brand-icon {
            width: 42px;
            height: 42px;
            padding: 4px;
            margin-bottom: 10px;
        }

        .navbar .nav-link {
            color: #fff !important;
            margin-left: 20px;
            font-weight: 600;
        }

        .navbar .nav-link:hover {
            opacity: 0.85;
            transition: opacity 0.3s;
        }

        .footer {
            background-color: var(--kfc-dark);
            color: #fff;
            padding: 50px 0 30px;
            margin-top: 80px;
        }

        .footer p {
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.8;
        }

        .footer h5 {
            font-family: var(--font-helmet);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .btn {
            border-radius: 12px;
            font-weight: 700;
        }

        .btn-kfc {
            background-color: var(--kfc-red);
            border: 3px solid var(--kfc-black);
            color: #fff;
            padding: 12px 30px;
            min-width: 120px;
            box-shadow: 5px 5px 0 rgba(38, 28, 2, 0.22);
            transition: all 0.25s ease;
        }

        .btn-kfc:hover {
            background-color: #A10F28;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 7px 7px 0 rgba(38, 28, 2, 0.3);
        }

        .btn-kfc-outline {
            border: 3px solid var(--kfc-red);
            color: var(--kfc-red);
            background: transparent;
            padding: 10px 28px;
        }

        .btn-kfc-outline:hover {
            background: var(--kfc-red);
            color: #fff;
        }

        .card {
            border: 3px solid var(--kfc-black);
            border-radius: 20px;
            box-shadow: 8px 8px 0 rgba(38, 28, 2, 0.25);
            transition: all 0.25s ease;
            margin-bottom: 0;
            background: #fff;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 8px 8px 0 rgba(38, 28, 2, 0.25);
        }

        .menu-card-wrapper,
        .selection-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .menu-card-wrapper:hover,
        .selection-card:hover {
            transform: translateY(-3px);
            box-shadow: 12px 12px 0 rgba(38, 28, 2, 0.35);
            z-index: 2;
            position: relative;
        }

        .card-body {
            padding: 20px;
        }

        .card-body h5,
        .card-body h6 {
            margin-bottom: 15px;
        }

        .card-body p {
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .card-body p:last-child {
            margin-bottom: 0;
        }

        .menu-item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 16px 16px 0 0;
            display: block;
        }

        .badge-price {
            background: var(--kfc-red);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 10px 15px;
            border-radius: 14px;
            display: inline-block;
            margin-bottom: 15px;
            border: 2px solid var(--kfc-black);
        }

        .badge-addon {
            background: var(--kfc-red);
            color: #fff;
            margin-right: 8px;
            margin-bottom: 8px;
            padding: 8px 14px;
            border-radius: 12px;
            border: 2px solid var(--kfc-black);
            display: inline-block;
        }

        .page-title {
            font-family: var(--font-helmet);
            color: var(--kfc-red);
            font-weight: 800;
            margin: 20px 0 40px;
            text-align: center;
            font-size: 2rem;
            letter-spacing: 0.5px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .card-title,
        .btn-kfc,
        .btn-kfc-outline,
        .kiosk-nav-link,
        .kiosk-subtotal {
            font-family: var(--font-helmet);
        }

        .content-section {
            margin-bottom: 40px;
        }

        .search-section {
            margin-bottom: 50px;
            padding: 20px 0;
        }

        .menu-grid {
            margin-bottom: 30px;
            row-gap: 35px;
        }

        .menu-grid > [class*="col-"] {
            position: relative;
            z-index: 1;
        }

        .menu-card-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .menu-item-name {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .menu-item-description {
            font-size: 0.9rem;
            color: #5f5547;
            margin-bottom: 15px;
            min-height: 40px;
            line-height: 1.5;
        }

        .price-section {
            background: var(--kfc-light);
            border: 2px solid var(--kfc-black);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        .form-label {
            font-weight: 700;
            color: var(--kfc-dark);
            margin-bottom: 12px;
            display: block;
        }

        .form-control,
        .form-select {
            border: 2px solid var(--kfc-black);
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--kfc-red);
            box-shadow: 0 0 0 0.2rem rgba(196, 18, 48, 0.2);
        }

        .input-group-lg .form-control,
        .input-group-lg .form-select {
            padding: 15px 20px;
            font-size: 1.1rem;
            height: auto;
            margin-bottom: 0;
            border-width: 3px;
        }

        .input-group-lg .btn {
            padding: 15px 30px;
            font-size: 1rem;
        }

        .form-check {
            padding-left: 0;
            margin-bottom: 15px;
        }

        .form-check-input {
            margin-top: 6px;
            margin-right: 12px;
        }

        .form-check-label {
            padding-left: 8px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .modal-content {
            border: 3px solid var(--kfc-black);
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            border-bottom: 2px solid var(--kfc-black);
            border-top-left-radius: 17px !important;
            border-top-right-radius: 17px !important;
        }

        .card-footer {
            border-top: 2px solid var(--kfc-black);
            border-bottom-left-radius: 17px !important;
            border-bottom-right-radius: 17px !important;
        }

        .modal-header {
            background: var(--kfc-red);
            color: #fff;
            padding: 25px;
            border-bottom: 3px solid var(--kfc-black);
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 30px;
            background: #fff;
        }

        .modal-footer {
            padding: 20px;
            gap: 10px;
            border-top: 2px solid var(--kfc-black);
            background: var(--kfc-light);
        }

        .cart-item {
            border-bottom: 2px solid var(--kfc-black);
            padding: 25px 0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .alert {
            border-radius: 15px;
            border: 2px solid var(--kfc-black);
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 4px 4px 0 rgba(38, 28, 2, 0.16);
        }

        .success-icon,
        .error-icon {
            width: 100px;
            height: 100px;
            margin: 20px auto;
        }

        .kiosk-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--kfc-red);
            color: #fff;
            padding: 12px 0;
            box-shadow: 0 -6px 20px rgba(38, 28, 2, 0.3);
            z-index: 1000;
        }

        .kiosk-footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            height: 70px;
        }

        .kiosk-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
        }

        .kiosk-logo-icon {
            width: 45px;
            height: 45px;
            object-fit: contain;
            border-radius: 12px;
            background: #fff;
            border: 2px solid var(--kfc-black);
            padding: 3px;
        }

        .kiosk-logo span {
            font-family: var(--font-kfc-script);
            font-weight: 400;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .kiosk-nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .kiosk-nav-link {
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .kiosk-nav-link:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .kiosk-subtotal {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.22);
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.5);
            font-weight: 700;
        }

        .kiosk-subtotal-amount {
            font-size: 1.3rem;
            font-weight: 800;
        }

        .kiosk-nav .bg-white {
            border: 2px solid var(--kfc-black);
            border-radius: 12px;
            box-shadow: 4px 4px 0 rgba(38, 28, 2, 0.2);
        }

        body.kiosk-mode {
            padding-bottom: 130px;
        }

        body.kiosk-mode .navbar,
        body.kiosk-mode .footer {
            display: none;
        }

        body.kiosk-mode main .container > div:last-child {
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            body.kiosk-mode {
                padding-bottom: 180px;
            }

            .kiosk-mode main .container {
                padding: 0 15px;
            }

            .kiosk-footer-content {
                flex-wrap: wrap;
                height: auto;
                gap: 12px;
            }

            .kiosk-nav {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 12px;
            }
        }
    </style>
    @yield('extra-css')
</head>
<body @if(Request::routeIs('ordering.menu', 'ordering.cart')) class="kiosk-mode" @endif>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('ordering.selection') }}">
                <img src="{{ asset('assets/images/KFC_logo_full_icon.png') }}" alt="KFC Icon" class="navbar-brand-icon">
                <span>KFC</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    @if(Request::routeIs('ordering.menu', 'ordering.cart', 'ordering.checkout'))
                        <a class="nav-link" href="{{ route('ordering.cart', ['mode' => request()->query('mode', 'dine-in')]) }}">
                            🛒 Cart
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Kiosk Mode Bottom Navigation -->
    @if(Request::routeIs('ordering.menu', 'ordering.cart'))
        <footer class="kiosk-footer">
            <div class="kiosk-footer-content">
                <!-- Logo -->
                <a href="{{ route('ordering.selection') }}" class="kiosk-logo">
                    <img src="{{ asset('assets/images/KFC_logo_full_icon.png') }}" alt="KFC" class="kiosk-logo-icon">
                    <span>KFC</span>
                </a>

                <!-- Navigation & Cart Info -->
                <div class="kiosk-nav">
                    <!-- Back to Menu -->
                    @if(Request::routeIs('ordering.cart'))
                        <a href="{{ route('ordering.menu', ['mode' => $mode ?? 'dine-in']) }}" class="kiosk-nav-link">
                            ← Back to Menu
                        </a>
                    @endif

                    <!-- Subtotal Display -->
                    @if(isset($cartSubtotal) && $cartSubtotal > 0)
                        <div class="kiosk-subtotal">
                            <span>Subtotal:</span>
                            <span class="kiosk-subtotal-amount">₱{{ number_format($cartSubtotal, 2) }}</span>
                        </div>
                    @endif

                    <!-- Proceed to Cart (from menu) or Checkout (from cart) -->
                    @if(Request::routeIs('ordering.menu') && isset($cartSubtotal) && $cartSubtotal > 0)
                        <a href="{{ route('ordering.cart', ['mode' => $mode ?? 'dine-in']) }}" class="kiosk-nav-link bg-white text-danger fw-bold">
                            🛒 View Cart
                        </a>
                    @elseif(Request::routeIs('ordering.cart'))
                        <a href="{{ route('ordering.checkout', ['mode' => $mode ?? 'dine-in']) }}" class="kiosk-nav-link bg-white text-danger fw-bold">
                            Proceed to Checkout →
                        </a>
                    @endif
                </div>
            </div>
        </footer>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('extra-js')
</body>
</html>
