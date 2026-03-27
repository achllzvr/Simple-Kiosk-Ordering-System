@extends('ordering.layout')

@section('title', 'Checkout - KFC')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title">Checkout</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <h6 class="mb-2">Please fix the following:</h6>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('place-order') }}">
                    @csrf
                    <input type="hidden" name="mode" value="{{ $mode }}">

                    <!-- Dine-in/Take-out Information -->
                    <div class="mb-4">
                        <h5 class="kfc-heading">
                            📍 Order Information
                        </h5>

                        @if($mode === 'dine-in')
                            <!-- Dine-in details -->
                            <fieldset class="checkout-fieldset mb-3">
                                <legend class="form-label mb-3">Table / Seat Information <span class="text-danger">*</span></legend>

                                <label class="payment-option" for="seating_available">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="seating_option" 
                                        id="seating_available" 
                                        value="available"
                                        checked
                                    >
                                    <span>I have a table/seat number</span>
                                </label>

                                <div class="mb-3 mt-3" id="seat_input_group">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="address" 
                                        name="address" 
                                        placeholder="Enter your table or seat number"
                                    >
                                    <small class="text-muted d-block mt-2">This helps staff deliver your order to your table.</small>
                                </div>

                                <label class="payment-option" for="seating_unavailable">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="seating_option" 
                                        id="seating_unavailable" 
                                        value="unavailable"
                                    >
                                    <span>No available number - Serve at my convenience</span>
                                </label>
                            </fieldset>
                        @else
                            <!-- Take-out details -->
                            <div class="alert alert-light border">
                                <h6>🏪 Take-Out Details</h6>
                                <p class="mb-0 small">
                                    Your order will be ready for collection at:<br>
                                    <strong>KFC Main Branch</strong><br>
                                    123 Food Street, Downtown<br>
                                    Phone: 1-800-KFC-FOOD
                                </p>
                            </div>
                            <input type="hidden" name="address" value="Take-Out at KFC Main Branch">
                        @endif
                    </div>

                    <hr>

                    <!-- Payment Method -->
                    <div class="mb-4">
                        <h5 class="kfc-heading">
                            💳 Payment Method
                        </h5>

                        <div class="list-group checkout-list-group">
                            <label class="list-group-item payment-option">
                                <input 
                                    class="form-check-input me-2" 
                                    type="radio" 
                                    name="payment_method" 
                                    value="credit_card" 
                                    checked
                                >
                                <span>💳 Credit/Debit Card</span>
                            </label>
                            <label class="list-group-item payment-option">
                                <input 
                                    class="form-check-input me-2" 
                                    type="radio" 
                                    name="payment_method" 
                                    value="online_banking"
                                >
                                <span>🏦 Online Banking</span>
                            </label>
                            <label class="list-group-item payment-option">
                                <input 
                                    class="form-check-input me-2" 
                                    type="radio" 
                                    name="payment_method" 
                                    value="digital_wallet"
                                >
                                <span>📱 Digital Wallet</span>
                            </label>
                            <label class="list-group-item payment-option">
                                <input 
                                    class="form-check-input me-2" 
                                    type="radio" 
                                    name="payment_method" 
                                    value="cash_payment"
                                >
                                <span>💵 Cash Payment</span>
                            </label>
                        </div>
                    </div>

                    <hr>

                    <!-- Order Agreement -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="agreement" 
                                name="agreement" 
                                required
                            >
                            <label class="form-check-label" for="agreement">
                                I agree to the terms and conditions and confirm my order details
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-kfc btn-lg">
                            Place Order
                        </button>
                        <a href="{{ route('ordering.cart', ['mode' => $mode]) }}" class="btn btn-outline-secondary">
                            Back to Cart
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Box -->
        <div class="alert alert-info mt-4 info-box">
            <h6 class="kfc-text-red mb-3">ℹ️ Order Information</h6>
            <ul class="small mb-0">
                <li>Your order will be processed after payment confirmation</li>
                <li>A confirmation email will be sent to your registered email address</li>
                <li>Estimated 
                    @if($mode === 'dine-in')
                        dine-in serving time is 15-25 minutes
                    @else
                        take-out preparation time is 15-30 minutes
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    .kfc-text-red {
        color: var(--kfc-red);
    }

    .kfc-heading {
        color: var(--kfc-red);
        font-weight: 700;
        margin-bottom: 20px;
    }

    .info-box {
        border-radius: 8px;
    }

    .list-group-item {
        border: 2px solid var(--kfc-black);
        border-radius: 12px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
        padding: 14px 16px;
    }

    .list-group-item:hover {
        background-color: rgba(245, 212, 183, 0.25);
    }

    .list-group-item input[type="radio"]:checked {
        border-color: var(--kfc-red);
    }

    .payment-option {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    .payment-option .form-check-input {
        margin-top: 0;
        margin-right: 0;
        flex-shrink: 0;
    }

    .checkout-fieldset {
        border: 2px solid var(--kfc-black);
        border-radius: 12px;
        padding: 16px;
        background: #fff;
    }

    .checkout-list-group {
        gap: 8px;
        display: flex;
        flex-direction: column;
    }
</style>

@endsection
