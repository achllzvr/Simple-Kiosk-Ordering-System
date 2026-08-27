@extends('ordering.layout')

@section('title', 'Checkout - KFC')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title">Checkout</h1>
    </div>
</div>

        <div class="row justify-content-center">
    <div class="col-lg-6 col-xl-5">
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

        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('place-order') }}" class="js-guard-submit">
                    @csrf
                    <input type="hidden" name="mode" value="{{ $mode }}">

                    <div class="mb-4">
                        <h2 class="h5 kfc-heading">Contact Details</h2>
                        <div class="mb-3">
                            <label class="form-label" for="guest_name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="guest_name" name="guest_name" value="{{ old('guest_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="guest_phone">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="guest_email">Email (optional)</label>
                            <input type="email" class="form-control" id="guest_email" name="guest_email" value="{{ old('guest_email') }}">
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2 class="h5 kfc-heading">Order Information</h2>

                        @if($mode === 'dine-in')
                            <fieldset class="checkout-fieldset mb-3">
                                <legend class="form-label mb-3">Table / Seat Information</legend>
                                <label class="payment-option" for="seating_available">
                                    <input class="form-check-input" type="radio" name="seating_option" id="seating_available" value="available" checked>
                                    <span>I have a table/seat number</span>
                                </label>
                                <div class="mb-3 mt-3" id="seat_input_group">
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter your table or seat number" value="{{ old('address') }}">
                                </div>
                                <label class="payment-option" for="seating_unavailable">
                                    <input class="form-check-input" type="radio" name="seating_option" id="seating_unavailable" value="unavailable">
                                    <span>No available number - Serve at my convenience</span>
                                </label>
                            </fieldset>
                        @elseif($mode === 'delivery')
                            <div class="alert alert-light border">
                                <h6>Delivery Store</h6>
                                @if($restaurant)
                                    <p class="mb-0"><strong>{{ $restaurant->name }}</strong><br>{{ $restaurant->address }}</p>
                                @else
                                    <p class="mb-0 text-danger">No store selected. <a href="{{ route('ordering.location', ['mode' => 'delivery']) }}">Choose a store</a></p>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-light border">
                                <h6>Take-Out</h6>
                                <p class="mb-0 small">Pick up at the counter when your order is ready.</p>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div class="mb-4">
                        <fieldset>
                            <legend class="h5 kfc-heading">Payment Method</legend>
                        <p class="small text-muted mb-2">
                            Total due: <strong>₱{{ number_format($total ?? 0, 2) }}</strong>
                            @if(!empty($paymongoEnabled))
                                · Online methods use PayMongo Hosted Checkout
                            @endif
                        </p>
                        <div class="list-group checkout-list-group" role="radiogroup" aria-label="Payment method">
                            <label class="list-group-item payment-option">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="credit_card" checked>
                                <span>Credit/Debit Card</span>
                            </label>
                            <label class="list-group-item payment-option">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="digital_wallet">
                                <span>Digital Wallet (GCash / Maya)</span>
                            </label>
                            <label class="list-group-item payment-option">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="online_banking">
                                <span>Online Banking</span>
                            </label>
                            <label class="list-group-item payment-option">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="cash_payment">
                                <span>Cash Payment</span>
                            </label>
                        </div>
                        </fieldset>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreement" name="agreement" required>
                            <label class="form-check-label" for="agreement">
                                I agree to the terms and conditions and confirm my order details
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-kfc btn-lg">Place Order</button>
                        <a href="{{ route('ordering.cart', ['mode' => $mode]) }}" class="btn btn-outline-secondary">Back to Cart</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .kfc-heading { color: var(--kfc-red); font-weight: 700; margin-bottom: 20px; }
    .list-group-item { border: 2px solid var(--kfc-black); border-radius: 12px; margin-bottom: 10px; cursor: pointer; background: #fff; padding: 14px 16px; }
    .payment-option { display: flex; align-items: center; gap: 10px; width: 100%; }
    .checkout-fieldset { border: 2px solid var(--kfc-black); border-radius: 12px; padding: 16px; background: #fff; }
    .checkout-list-group { gap: 8px; display: flex; flex-direction: column; }
</style>
@endsection
