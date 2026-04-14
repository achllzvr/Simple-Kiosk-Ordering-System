@extends('ordering.layout')

@section('title', 'Cart - KFC')

@section('content')

<div class="row mb-5">
    <div class="col-12">
        <h1 class="page-title">Shopping Cart</h1>
    </div>
</div>

<div class="row g-5 justify-content-center">
    <!-- Cart Items Section -->
    <div class="col-lg-8 mb-4">
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        @if(count($cartItems) > 0)
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @foreach($cartItems as $item)
                        <div class="cart-item px-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="kfc-text-red fw-bold mb-3">{{ $item['itemName'] }}</h5>
                                    
                                    @if(isset($item['variation']) && $item['variation'])
                                        <p class="small mb-2 text-muted">
                                            <strong>Variation:</strong> {{ $item['variation'] }}
                                        </p>
                                    @endif

                                    @if(isset($item['addons']) && count($item['addons']) > 0)
                                        <p class="small mb-2 text-muted">
                                            <strong>Add-ons:</strong> {{ implode(', ', $item['addons']) }}
                                        </p>
                                    @endif

                                    <div class="quantity-control-wrapper mt-3">
                                        <p class="small mb-2 text-muted"><strong>Quantity:</strong></p>
                                        <form method="POST" action="{{ route('ordering.cart.updateQuantity') }}" class="quantity-form">
                                            @csrf
                                            <input type="hidden" name="cart_index" value="{{ $item['cartIndex'] }}">
                                            <input type="hidden" name="mode" value="{{ $mode }}">

                                            <button type="submit" name="action" value="decrease" class="btn btn-outline-secondary btn-sm quantity-btn" aria-label="Decrease quantity">−</button>

                                            <input
                                                type="number"
                                                name="quantity"
                                                value="{{ $item['quantity'] }}"
                                                min="1"
                                                max="99"
                                                class="form-control form-control-sm quantity-input"
                                                aria-label="Item quantity"
                                            >

                                            <button type="submit" name="action" value="increase" class="btn btn-outline-secondary btn-sm quantity-btn" aria-label="Increase quantity">+</button>
                                        </form>

                                        <form method="POST" action="{{ route('ordering.cart.updateQuantity') }}" class="remove-form mt-2">
                                            @csrf
                                            <input type="hidden" name="cart_index" value="{{ $item['cartIndex'] }}">
                                            <input type="hidden" name="mode" value="{{ $mode }}">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="btn btn-outline-danger btn-sm remove-btn">Remove from cart</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="col-md-6 text-end">
                                    <div class="mb-2">
                                        <h6 class="kfc-text-red fw-bold mb-2">₱{{ $item['price'] }} each</h6>
                                        <p class="mb-0 cart-total-price fw-bold fs-5">
                                            ₱{{ $item['total'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="alert alert-info text-center px-5 py-5">
                <h5 class="mb-3">Your cart is empty</h5>
                <p class="mb-4 text-muted">Add some delicious items from our menu</p>
                <a href="{{ route('ordering.menu', ['mode' => $mode]) }}" class="btn btn-kfc btn-lg">
                    Continue Shopping
                </a>
            </div>
        @endif
    </div>

    <!-- Order Summary Section -->
    @if(count($cartItems) > 0)
        <div class="col-lg-4">
            <div class="card shadow-sm cart-sticky">
                <div class="card-header kfc-header-bg">
                    <h5 class="mb-0 fw-bold">Order Summary</h5>
                </div>

                <div class="card-body p-4">
                    <!-- Order Details -->
                    <div class="row mb-4">
                        <div class="col-6 fw-500">Subtotal:</div>
                        <div class="col-6 text-end fw-bold">₱{{ number_format($subtotal, 2) }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6 fw-500">Tax (10%):</div>
                        <div class="col-6 text-end fw-500">₱{{ number_format($tax, 2) }}</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6 fw-500">Service Fee:</div>
                        <div class="col-6 text-end fw-500">₱{{ number_format($serviceFee, 2) }}</div>
                    </div>

                    <hr class="my-4">

                    <!-- Total -->
                    <div class="row mb-5">
                        <div class="col-6">
                            <h5 class="kfc-text-red fw-bold mb-0">Total:</h5>
                        </div>
                        <div class="col-6 text-end">
                            <h5 class="kfc-text-red fw-bold mb-0">₱{{ number_format($total, 2) }}</h5>
                        </div>
                    </div>

                    <!-- Mode Info -->
                    <div class="alert alert-light border mb-5 p-3">
                        <strong class="d-block mb-2">Order Mode:</strong>
                        @if($mode === 'dine-in')
                            <span>🍽️ Dine-In</span>
                        @else
                            <span>🏪 Take-Out</span>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-3">
                        <a href="{{ route('ordering.checkout', ['mode' => $mode]) }}" class="btn btn-kfc btn-lg">
                            Proceed to Checkout
                        </a>
                        <a href="{{ route('ordering.menu', ['mode' => $mode]) }}" class="btn btn-outline-secondary btn-lg">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .kfc-text-red {
        color: var(--kfc-red);
    }

    .cart-total-price {
        font-size: 1.2rem;
        font-weight: 700;
    }

    .cart-sticky {
        position: sticky;
        top: 20px;
        z-index: 10;
    }

    .kfc-header-bg {
        background-color: var(--kfc-red);
        color: white;
    }

    .quantity-control-wrapper {
        max-width: 340px;
    }

    .quantity-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .quantity-btn {
        min-width: 36px;
        height: 36px;
        padding: 0;
        line-height: 1;
        font-size: 1.1rem;
    }

    .quantity-input {
        width: 82px;
        text-align: center;
        margin-bottom: 0;
    }

    .remove-form {
        margin-left: 0;
    }

    .remove-btn {
        min-width: auto;
        padding: 6px 12px;
        border-width: 2px;
    }
</style>

@endsection

@section('extra-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.quantity-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const form = input.closest('form');
                if (!form) {
                    return;
                }

                let actionField = form.querySelector('input[name="action"]');
                if (!actionField) {
                    actionField = document.createElement('input');
                    actionField.type = 'hidden';
                    actionField.name = 'action';
                    form.appendChild(actionField);
                }
                actionField.value = 'set';

                const value = parseInt(input.value || '1', 10);
                if (Number.isNaN(value) || value < 1) {
                    input.value = 1;
                } else if (value > 99) {
                    input.value = 99;
                }

                form.submit();
            });
        });
    });
</script>
@endsection
