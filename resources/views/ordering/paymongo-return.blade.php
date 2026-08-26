@extends('ordering.layout')

@section('title', 'Payment Return - KFC')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body text-center p-5">
                @if($status === 'cancel')
                    <h1 class="kfc-heading">Payment Cancelled</h1>
                    <p class="text-muted">You left PayMongo checkout. Your order is still unpaid.</p>
                @else
                    <h1 class="kfc-heading">Thanks — checking payment</h1>
                    <p class="text-muted">
                        If payment succeeded, it will be confirmed by webhook (or an admin refresh).
                        This page does not mark the order paid by itself.
                    </p>
                @endif

                @if($order)
                    <div class="alert alert-light border text-start mt-3">
                        <p class="mb-1"><strong>Order #{{ $order->id }}</strong></p>
                        <p class="mb-1">Payment status: {{ $order->payment_status }}</p>
                        <p class="mb-0">Tracking token: <code>{{ $order->tracking_token }}</code></p>
                    </div>
                    <a href="{{ route('order.track', ['token' => $order->tracking_token]) }}" class="btn btn-kfc mt-3">Track Order</a>
                @elseif($token)
                    <a href="{{ route('order.track', ['token' => $token]) }}" class="btn btn-kfc mt-3">Track Order</a>
                @endif

                <a href="{{ route('ordering.selection') }}" class="btn btn-outline-secondary mt-3">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection
