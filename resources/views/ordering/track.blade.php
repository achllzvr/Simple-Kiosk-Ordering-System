@extends('ordering.layout')

@section('title', 'Track Order - KFC')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h1 class="page-title">Track Order</h1>
        <form method="GET" action="{{ route('order.track') }}" class="card mb-4">
            <div class="card-body">
                <label class="form-label" for="token">Tracking token</label>
                <input type="text" class="form-control mb-3" id="token" name="token" value="{{ $token }}" required>
                <button type="submit" class="btn btn-kfc">Look up</button>
            </div>
        </form>

        @if($token !== '' && !$order)
            <div class="alert alert-warning">No order found for that token.</div>
        @endif

        @if($order)
            <div class="card">
                <div class="card-body">
                    <h5 class="kfc-heading">Order #{{ $order->id }}</h5>
                    <p class="mb-1"><strong>Name:</strong> {{ $order->guest_name }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ $order->status }}</p>
                    <p class="mb-1"><strong>Payment:</strong> {{ $order->payment_status }}</p>
                    <p class="mb-1"><strong>Mode:</strong> {{ $order->order_mode }}</p>
                    @if($order->restaurant)
                        <p class="mb-1"><strong>Store:</strong> {{ $order->restaurant->name }}</p>
                    @endif
                    <p class="mb-3"><strong>Total:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                    <ul class="list-group">
                        @foreach($order->items as $item)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $item->menuItem->name ?? 'Item' }} × {{ $item->quantity }}</span>
                                <span>₱{{ number_format($item->price_at_purchase * $item->quantity, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
