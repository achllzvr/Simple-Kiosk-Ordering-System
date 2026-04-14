@extends('ordering.layout')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="kfc-heading">Admin Dashboard</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('logout') }}" class="btn btn-kfc" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light border-kfc">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Users</h6>
                    <h3 class="kfc-heading">{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-kfc">
                <div class="card-body text-center">
                    <h6 class="text-muted">Active Menu Items</h6>
                    <h3 class="kfc-heading">{{ $totalMenuItems }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-kfc">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pending Orders</h6>
                    <h3 class="kfc-heading text-warning">{{ $pendingOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-kfc">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3 class="kfc-heading text-success">${{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="kfc-heading mb-3">Quick Actions</h3>
            <div class="btn-group w-100" role="group">
                <a href="{{ route('users.index') }}" class="btn btn-kfc flex-fill">Manage Users</a>
                <a href="{{ route('admin.menu.index') }}" class="btn btn-kfc flex-fill">Manage Menu</a>
                <a href="{{ route('admin.orders') }}" class="btn btn-kfc flex-fill">View Orders</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="kfc-heading mb-3">Recent Orders</h3>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-kfc">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Mode</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ ucfirst(str_replace('-', ' ', $order->order_mode)) }}</td>
                            <td>${{ number_format($order->total_price, 2) }}</td>
                            <td>
                                <span class="badge bg-kfc">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No orders yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .border-kfc {
        border-color: #cc0000 !important;
    }
    .table-kfc {
        background-color: #cc0000 !important;
        color: white;
    }
</style>
@endsection
