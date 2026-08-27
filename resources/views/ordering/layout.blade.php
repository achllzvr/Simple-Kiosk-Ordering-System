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
            --shadow-offset: 12px;
            --layer-base: 1;
            --layer-raised: 5;
            --layer-sticky: 20;
            --layer-nav: 1030;
            --layer-kiosk: 1040;
            --layer-modal: 1060;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: var(--kfc-light);
            color: var(--kfc-dark);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        main {
            padding: 1.25rem 0 2.5rem;
            overflow: visible;
            position: relative;
            z-index: var(--layer-base);
        }

        main .container,
        main .page-shell {
            padding-left: max(1.25rem, var(--shadow-offset));
            padding-right: max(1.25rem, var(--shadow-offset));
            overflow: visible;
        }

        .kiosk-mode main {
            padding: 2rem 0 2.5rem;
        }

        .kiosk-mode main .container,
        .kiosk-mode main .page-shell {
            padding-left: max(1.5rem, var(--shadow-offset));
            padding-right: max(1.5rem, var(--shadow-offset));
        }

        .navbar {
            background-color: var(--kfc-red);
            box-shadow: 0 4px 14px rgba(196, 18, 48, 0.25);
            position: relative;
            z-index: var(--layer-nav);
        }

        .navbar .collapse.show,
        .navbar .collapsing {
            z-index: var(--layer-nav);
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
            box-shadow: none;
            filter: drop-shadow(8px 8px 0 rgba(38, 28, 2, 0.25));
            transition: transform 0.25s ease, filter 0.25s ease;
            margin-bottom: 0;
            background: #fff;
            overflow: hidden;
            position: relative;
            z-index: var(--layer-base);
        }

        .card > .card-body,
        .card > .card-footer,
        .card > .card-header,
        .card > .list-group,
        .card > .table-responsive,
        .card > table {
            background: #fff;
        }

        .card > :first-child,
        .card > .card-img-top:first-child,
        .card > img:first-child,
        .card > .menu-item-image:first-child {
            border-top-left-radius: 17px;
            border-top-right-radius: 17px;
        }

        .card > .card-header:first-child {
            border-top-left-radius: 17px !important;
            border-top-right-radius: 17px !important;
        }

        .card > .card-footer:last-child,
        .card > .card-body:last-child {
            border-bottom-left-radius: 17px;
            border-bottom-right-radius: 17px;
        }

        .card:hover {
            filter: drop-shadow(8px 8px 0 rgba(38, 28, 2, 0.25));
        }

        .menu-card-wrapper,
        .selection-card {
            transition: transform 0.25s ease, filter 0.25s ease;
            overflow: hidden;
        }

        .menu-card-wrapper:hover,
        .selection-card:hover {
            transform: translateY(-4px);
            filter: drop-shadow(12px 12px 0 rgba(38, 28, 2, 0.35));
            z-index: var(--layer-raised);
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

        .menu-item-image,
        .card > .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 17px;
            border-top-right-radius: 17px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
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
            margin: 0.5rem auto 1.5rem;
            text-align: center;
            font-size: clamp(1.6rem, 2.4vw, 2rem);
            letter-spacing: 0.5px;
            max-width: 40rem;
            line-height: 1.2;
        }

        .page-title.text-md-start {
            margin-left: 0;
            margin-right: 0;
        }

        @media (min-width: 768px) {
            .page-title.text-md-start {
                text-align: left;
                max-width: none;
            }
        }

        .kfc-heading {
            font-family: var(--font-helmet);
            color: var(--kfc-red);
            font-weight: 800;
        }

        .btn-outline-kfc {
            border: 3px solid var(--kfc-red);
            color: var(--kfc-red);
            background: transparent;
            padding: 0.5rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
        }

        .btn-outline-kfc:hover {
            background: var(--kfc-red);
            color: #fff;
        }

        .bg-kfc {
            background-color: var(--kfc-red) !important;
            color: #fff !important;
        }

        .border-kfc {
            border-color: var(--kfc-red) !important;
        }

        .table-kfc {
            background-color: var(--kfc-red) !important;
            color: #fff;
        }

        .table-kfc th {
            color: #fff;
            font-weight: 700;
        }

        .admin-page {
            padding-top: 0.5rem;
            padding-bottom: 1.5rem;
        }

        .stat-card .card-body {
            padding: 1.25rem 1rem;
        }

        .stat-card h6 {
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
        }

        .stat-card h3 {
            margin-bottom: 0;
            font-size: 1.75rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        @media (min-width: 768px) {
            .quick-actions {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .btn.is-loading,
        .btn:disabled {
            opacity: 0.7;
            pointer-events: none;
        }

        .visually-hidden-focusable:not(:focus):not(:focus-within) {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }

            .menu-card-wrapper:hover,
            .selection-card:hover,
            .btn-kfc:hover,
            .kiosk-nav-link:hover {
                transform: none;
            }
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
        .btn-outline-kfc,
        .kiosk-nav-link,
        .kiosk-subtotal {
            font-family: var(--font-helmet);
        }

        .content-section {
            margin-bottom: 40px;
        }

        .search-section {
            margin-bottom: 2.5rem;
            padding: 0.5rem 0 1rem;
            overflow: visible;
        }

        .search-section .input-group,
        .search-section form {
            width: 100%;
            max-width: 42rem;
            margin-left: auto;
            margin-right: auto;
            align-items: stretch;
        }

        .search-section .form-control {
            margin-bottom: 0;
            min-width: 0;
        }

        .menu-grid {
            margin-bottom: 2rem;
            row-gap: 2.75rem !important;
            overflow: visible;
            padding-bottom: 0.75rem;
        }

        .menu-grid > [class*="col-"] {
            position: relative;
            z-index: var(--layer-base);
            overflow: visible;
            display: flex;
        }

        .menu-grid > [class*="col-"]:hover,
        .menu-grid > [class*="col-"]:focus-within {
            z-index: var(--layer-raised);
        }

        .menu-card-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            width: 100%;
        }

        .selection-stage {
            min-height: calc(100vh - 7.5rem);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: stretch;
            padding: 1.5rem 0 2.5rem;
            overflow: visible;
        }

        .selection-stage .selection-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .selection-stage .selection-grid {
            --bs-gutter-x: 1.75rem;
            --bs-gutter-y: 1.75rem;
            overflow: visible;
            padding-bottom: 0.75rem;
        }

        .selection-stage .selection-grid > [class*="col-"] {
            display: flex;
            overflow: visible;
            position: relative;
            z-index: var(--layer-base);
        }

        .selection-stage .selection-grid > [class*="col-"]:hover {
            z-index: var(--layer-raised);
        }

        .selection-stage .selection-card {
            width: 100%;
        }

        .page-shell > .row,
        .admin-page .row,
        .page-shell .g-3,
        .page-shell .g-4,
        .page-shell .g-5 {
            overflow: visible;
        }

        .page-shell > .row > [class*="col-"],
        .admin-page .row > [class*="col-"] {
            overflow: visible;
        }

        .kanban-board {
            overflow: visible;
        }

        .kanban-board > [class*="col-"] {
            overflow: visible;
            min-width: 0;
        }

        .kanban-board .card {
            max-height: none;
            overflow: hidden;
        }

        .kanban-board .card-body {
            overflow: visible;
        }

        .map-frame {
            height: 420px;
            border: 3px solid var(--kfc-black);
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            z-index: var(--layer-base);
            background: #fff;
        }

        .map-frame-sm {
            height: 320px;
            border-radius: 12px;
        }

        .form-card-narrow {
            max-width: 48rem;
            margin-left: auto;
            margin-right: auto;
        }

        .location-status {
            flex: 1 1 12rem;
            min-width: 0;
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

        .modal {
            z-index: 1055;
        }

        .modal-backdrop {
            z-index: 1050;
        }

        /* Keep kiosk chrome under Bootstrap modal + backdrop */
        .kiosk-footer {
            z-index: 1040;
        }

        .card-header {
            border-bottom: 2px solid var(--kfc-black);
        }

        .card > .card-header:first-child {
            border-top-left-radius: 17px !important;
            border-top-right-radius: 17px !important;
        }

        .card-footer {
            border-top: 2px solid var(--kfc-black);
        }

        .card > .card-footer:last-child {
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
            z-index: var(--layer-kiosk);
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

        body.kiosk-mode main .container > div:last-child,
        body.kiosk-mode main .page-shell > div:last-child {
            margin-bottom: 2.5rem;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            opacity: 0;
            animation: fade-in-up 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: var(--fade-delay, 0ms);
        }

        .cart-sticky {
            position: sticky;
            top: 1.25rem;
            z-index: var(--layer-sticky);
        }

        @media (max-width: 768px) {
            body.kiosk-mode {
                padding-bottom: 180px;
            }

            .kiosk-mode main .container,
            .kiosk-mode main .page-shell {
                padding-left: max(1rem, 8px);
                padding-right: max(1rem, 8px);
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

            .selection-stage {
                min-height: auto;
                justify-content: flex-start;
                padding-top: 1rem;
            }

            .menu-grid {
                row-gap: 2rem !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .fade-in-up {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>
    @yield('extra-css')
</head>
<body @if(Request::routeIs('ordering.menu', 'ordering.cart')) class="kiosk-mode" @endif>
    <a class="visually-hidden-focusable btn btn-light m-2" href="#main-content">Skip to content</a>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4" aria-label="Primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('ordering.selection') }}">
                <img src="{{ asset('assets/images/KFC_logo_full_icon.png') }}" alt="KFC" class="navbar-brand-icon">
                <span>KFC</span>
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a class="nav-link d-inline-block" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            <a class="nav-link d-inline-block" href="{{ route('admin.menu.index') }}">Menu</a>
                            <a class="nav-link d-inline-block" href="{{ route('admin.restaurants.index') }}">Stores</a>
                            <a class="nav-link d-inline-block" href="{{ route('admin.orders') }}">Orders</a>
                            <a class="nav-link d-inline-block" href="{{ route('users.index') }}">Users</a>
                        @endif
                        <a class="nav-link d-inline-block" href="{{ route('ordering.selection') }}">Ordering</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline-block ms-2 js-guard-submit">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light">Logout</button>
                        </form>
                    @else
                        <a class="nav-link d-inline-block" href="{{ route('ordering.selection') }}">Order Now</a>
                        <a class="nav-link d-inline-block" href="{{ route('order.track') }}">Track Order</a>
                        <a class="nav-link d-inline-block" href="{{ route('ordering.cart', ['mode' => request()->query('mode', 'dine-in')]) }}">Cart</a>
                        <a class="nav-link d-inline-block" href="{{ route('login') }}">Admin Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container page-shell">
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
                            View Cart
                        </a>
                    @elseif(Request::routeIs('ordering.cart'))
                        <a href="{{ route('ordering.checkout', ['mode' => $mode ?? 'dine-in']) }}" class="kiosk-nav-link bg-white text-danger fw-bold">
                            Proceed to Checkout
                        </a>
                    @endif
                </div>
            </div>
        </footer>
    @endif

    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmActionModalLabel">Confirm action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="confirmActionMessage">Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-kfc" id="confirmActionProceed">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            // Modals must live on <body>. Fade/transform ancestors trap them under the backdrop.
            document.querySelectorAll('.modal').forEach(function (el) {
                if (el.parentElement !== document.body) {
                    document.body.appendChild(el);
                }
            });

            document.querySelectorAll('form.js-guard-submit').forEach(function (form) {
                form.addEventListener('submit', function () {
                    var btn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (!btn || btn.disabled) return;
                    btn.disabled = true;
                    btn.classList.add('is-loading');
                    if (btn.tagName === 'BUTTON' && !btn.dataset.originalLabel) {
                        btn.dataset.originalLabel = btn.innerHTML;
                        btn.innerHTML = 'Please wait…';
                    }
                });
            });

            var pendingForm = null;
            var modalEl = document.getElementById('confirmActionModal');
            var messageEl = document.getElementById('confirmActionMessage');
            var proceedBtn = document.getElementById('confirmActionProceed');
            var modal = modalEl ? new bootstrap.Modal(modalEl) : null;

            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }
                    event.preventDefault();
                    pendingForm = form;
                    if (messageEl) {
                        messageEl.textContent = form.getAttribute('data-confirm') || 'Are you sure?';
                    }
                    if (modal) modal.show();
                });
            });

            if (proceedBtn) {
                proceedBtn.addEventListener('click', function () {
                    if (!pendingForm) return;
                    pendingForm.dataset.confirmed = '1';
                    if (modal) modal.hide();
                    pendingForm.submit();
                    pendingForm = null;
                });
            }

            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!reduceMotion) {
                var shell = document.querySelector('main .page-shell');
                if (shell) {
                    var targets = [];
                    shell.querySelectorAll(':scope > *').forEach(function (el) {
                        if (el.classList.contains('modal') || el.querySelector('.modal')) return;
                        targets.push(el);
                    });
                    shell.querySelectorAll(
                        '.menu-grid > [class*="col-"],' +
                        '.selection-grid > [class*="col-"],' +
                        '.kanban-board > [class*="col-"],' +
                        '.stat-card,' +
                        '.quick-actions > *,' +
                        '.admin-page > section,' +
                        '.cart-item,' +
                        '.alert'
                    ).forEach(function (el) {
                        if (el.classList.contains('modal') || el.querySelector('.modal')) return;
                        if (targets.indexOf(el) === -1) {
                            targets.push(el);
                        }
                    });

                    targets.forEach(function (el, index) {
                        if (el.classList.contains('fade-in-up')) return;
                        el.classList.add('fade-in-up');
                        el.style.setProperty('--fade-delay', Math.min(index * 55, 660) + 'ms');
                    });
                }

                document.querySelectorAll('.navbar, .kiosk-footer').forEach(function (el, index) {
                    el.classList.add('fade-in-up');
                    el.style.setProperty('--fade-delay', (index * 40) + 'ms');
                });
            }
        })();
    </script>
    @yield('extra-js')
</body>
</html>
