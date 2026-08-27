@extends('ordering.layout')

@section('title', 'Order Failed - KFC')

@section('content')
<div class="row">
    <div class="col-lg-7 mx-auto">
        <div class="card">
            <div class="card-body p-4 p-md-5 text-center">
                <h1 class="kfc-heading mb-3">Order Could Not Be Completed</h1>
                <p class="text-muted mb-4">Something went wrong while placing your order. Your cart is still available.</p>

                <div class="alert alert-danger text-start mb-4" role="alert">
                    <p class="fw-bold mb-1">What happened</p>
                    <p class="mb-0">{{ $errorMessage }}</p>
                </div>

                <div class="alert alert-light border text-start mb-4">
                    <p class="fw-bold mb-2">Try this next</p>
                    <ul class="mb-0 ps-3 small">
                        <li>Return to your cart and place the order again</li>
                        <li>Check your connection if PayMongo checkout failed to open</li>
                        <li>Use cash payment if online checkout is unavailable</li>
                    </ul>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('ordering.cart', ['mode' => $mode]) }}" class="btn btn-kfc btn-lg">Return to Cart</a>
                    <a href="{{ route('ordering.menu', ['mode' => $mode]) }}" class="btn btn-outline-secondary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
