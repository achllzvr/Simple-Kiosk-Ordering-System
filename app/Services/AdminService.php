<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminService
{
    /**
     * @return array{
     *   totalUsers: int,
     *   totalMenuItems: int,
     *   pendingOrders: int,
     *   totalRevenue: float|int|string,
     *   recentOrders: Collection<int, Order>
     * }
     */
    public function dashboardStats(): array
    {
        return [
            'totalUsers' => User::query()->count(),
            'totalMenuItems' => MenuItem::query()->where('is_active', true)->count(),
            'pendingOrders' => Order::query()->whereIn('status', ['placed', 'preparing'])->count(),
            'totalRevenue' => Order::query()->where('status', 'completed')->sum('total_price'),
            'recentOrders' => Order::query()
                ->with(['user', 'items'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];
    }
}
