@extends('ordering.layout')

@section('title', 'Admin Dashboard - KFC')

@section('content')
<div class="admin-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="page-title text-md-start mb-0">Admin Dashboard</h1>
        <form method="POST" action="{{ route('logout') }}" class="js-guard-submit">
            @csrf
            <button type="submit" class="btn btn-kfc">Logout</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card bg-light border-kfc stat-card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Users</h6>
                    <h3 class="kfc-heading">{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-light border-kfc stat-card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Active Menu Items</h6>
                    <h3 class="kfc-heading">{{ $totalMenuItems }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-light border-kfc stat-card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pending Orders</h6>
                    <h3 class="kfc-heading text-warning">{{ $pendingOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-light border-kfc stat-card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Completed Revenue</h6>
                    <h3 class="kfc-heading text-success">₱{{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <section class="mb-4" aria-labelledby="quick-actions-heading">
        <h2 id="quick-actions-heading" class="h4 kfc-heading mb-3">Quick Actions</h2>
        <div class="quick-actions">
            <a href="{{ route('users.index') }}" class="btn btn-kfc">Manage Users</a>
            <a href="{{ route('admin.menu.index') }}" class="btn btn-kfc">Manage Menu</a>
            <a href="{{ route('admin.restaurants.index') }}" class="btn btn-kfc">Manage Stores</a>
            <a href="{{ route('admin.orders') }}" class="btn btn-kfc">View Orders</a>
        </div>
    </section>

    <section aria-labelledby="recent-orders-heading">
        <h2 id="recent-orders-heading" class="h4 kfc-heading mb-3">Recent Orders</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-kfc">
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Mode</th>
                        <th scope="col">Total</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->guest_name ?: ($order->user->name ?? 'Guest') }}</td>
                        <td>{{ ucfirst(str_replace('-', ' ', $order->order_mode)) }}</td>
                        <td>₱{{ number_format($order->total_price, 2) }}</td>
                        <td>
                            <span class="badge bg-kfc">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No orders yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
