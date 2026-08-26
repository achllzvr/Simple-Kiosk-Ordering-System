<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class PayMongoReturnController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function __invoke(Request $request)
    {
        $token = $request->query('token');
        $status = $request->query('status', 'success');
        $order = $token ? $this->orderService->findByTrackingToken($token) : null;

        return view('ordering.paymongo-return', [
            'order' => $order,
            'token' => $token,
            'status' => $status,
        ]);
    }
}
