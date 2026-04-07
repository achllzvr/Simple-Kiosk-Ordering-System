@extends('ordering.layout')

@section('title', 'Order Kanban - KFC')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-2">
    <h1 class="page-title text-md-start mb-0">Order Management (Kanban)</h1>
    <a href="{{ route('users.index') }}" class="btn btn-kfc-outline">Manage Users</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-3">
    @foreach($columns as $status => $orders)
        <div class="col-lg-2-4 col-md-6">
            <div class="card h-100">
                <div class="card-header text-white {{ $status === 'placed' ? 'bg-secondary' : '' }} {{ $status === 'preparing' ? 'bg-warning text-dark' : '' }} {{ $status === 'ready' ? 'bg-primary' : '' }} {{ $status === 'completed' ? 'bg-success' : '' }} {{ $status === 'cancelled' ? 'bg-danger' : '' }}">
                    <h6 class="mb-0 text-uppercase">{{ str_replace('_', ' ', $status) }} ({{ count($orders) }})</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($orders as $order)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <h6 class="mb-2 fw-bold">{{ $order['order_id'] ?? 'Unknown' }}</h6>
                            <p class="small mb-1"><strong>Mode:</strong> {{ $order['mode'] ?? 'n/a' }}</p>
                            <p class="small mb-1"><strong>Payment:</strong> {{ str_replace('_', ' ', $order['payment_method'] ?? 'n/a') }}</p>
                            <p class="small mb-1"><strong>Items:</strong> {{ count($order['items'] ?? []) }}</p>
                            <p class="small mb-2"><strong>Placed:</strong> {{ $order['created_at'] ?? 'n/a' }}</p>

                            <form method="POST" action="{{ route('admin.orders.status') }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order['order_id'] ?? '' }}">
                                <select name="status" class="form-select form-select-sm mb-2" required>
                                    <option value="placed" @selected(($order['status'] ?? 'placed') === 'placed')>Placed</option>
                                    <option value="preparing" @selected(($order['status'] ?? '') === 'preparing')>Preparing</option>
                                    <option value="ready" @selected(($order['status'] ?? '') === 'ready')>Ready</option>
                                    <option value="completed" @selected(($order['status'] ?? '') === 'completed')>Completed</option>
                                    <option value="cancelled" @selected(($order['status'] ?? '') === 'cancelled')>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-kfc w-100">Update</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No orders in this column.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('extra-css')
<style>
    @media (min-width: 992px) {
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }
</style>
@endsection
