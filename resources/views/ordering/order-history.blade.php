@extends('ordering.layout')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="kfc-heading">My Orders</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('ordering.selection') }}" class="btn btn-kfc">Order Again</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-kfc text-white">
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Mode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><strong>#{{ $order->id }}</strong></td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        @foreach($order->items as $item)
                        <small>{{ $item->quantity }}x {{ $item->menuItem->name }}</small><br>
                        @endforeach
                    </td>
                    <td>${{ number_format($order->total_price, 2) }}</td>
                    <td>{{ ucfirst(str_replace('-', ' ', $order->order_mode)) }}</td>
                    <td>
                        @switch($order->status)
                            @case('placed')
                                <span class="badge bg-info">Placed</span>
                                @break
                            @case('preparing')
                                <span class="badge bg-warning">Preparing</span>
                                @break
                            @case('ready')
                                <span class="badge bg-success">Ready</span>
                                @break
                            @case('completed')
                                <span class="badge bg-success">Completed</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                                @break
                        @endswitch
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No orders yet. <a href="{{ route('ordering.selection') }}">Place your first order!</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .table-kfc {
        background-color: #cc0000 !important;
    }
</style>
@endsection
