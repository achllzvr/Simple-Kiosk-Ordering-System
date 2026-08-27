@extends('ordering.layout')

@section('title', 'Order Successful - KFC')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body text-center p-4 p-md-5">
                <div class="success-icon-mark mb-3" aria-hidden="true">✓</div>

                <h1 class="kfc-heading mb-3">Order Placed Successfully</h1>
                <p class="text-muted mb-4">
                    Thank you for your order. Save your order ID and tracking token below.
                </p>

                <div class="alert alert-success text-start mb-4">
                    <p class="mb-1 text-uppercase small fw-bold text-success">Order ID</p>
                    <p class="fs-3 fw-bold mb-3">#{{ $orderId }}</p>
                    @if(!empty($token))
                        <p class="mb-1"><strong>Tracking token</strong></p>
                        <p class="mb-2"><code class="user-select-all">{{ $token }}</code></p>
                        <a href="{{ route('order.track', ['token' => $token]) }}" class="fw-bold">Track this order</a>
                    @endif
                    @if($order)
                        <hr>
                        <p class="small mb-0">
                            Kitchen status: <strong>{{ ucfirst($order->status) }}</strong>
                            · Payment: <strong>{{ strtoupper($order->payment_status) }}</strong>
                            · Mode: {{ $order->order_mode }}
                            @if($order->restaurant)
                                · Store: {{ $order->restaurant->name }}
                            @endif
                        </p>
                    @endif
                </div>

                <div class="row g-3 mb-4 text-start">
                    <div class="col-md-6">
                        <div class="card h-100 status-card">
                            <div class="card-body">
                                <h2 class="h6 kfc-heading">Status</h2>
                                <p class="fs-5 fw-bold mb-1">{{ $order ? ucfirst($order->status) : 'Order received' }}</p>
                                <p class="small text-muted mb-0">We will update this as your kitchen prepares the order.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 status-card">
                            <div class="card-body">
                                <h2 class="h6 kfc-heading">Estimated time</h2>
                                <p class="fs-5 fw-bold mb-1">30–45 mins</p>
                                <p class="small text-muted mb-0">Typical preparation window for kiosk orders.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    @if(!empty($token))
                        <a href="{{ route('order.track', ['token' => $token]) }}" class="btn btn-kfc btn-lg">Track Order</a>
                    @endif
                    <a href="{{ route('ordering.selection') }}" class="btn btn-outline-secondary">Place Another Order</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .success-icon-mark {
        width: 4.5rem;
        height: 4.5rem;
        margin: 0 auto;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--kfc-red);
        color: #fff;
        font-size: 2rem;
        font-weight: 800;
        border: 3px solid var(--kfc-black);
    }

    .status-card {
        background: var(--kfc-light);
    }
</style>
@endsection
