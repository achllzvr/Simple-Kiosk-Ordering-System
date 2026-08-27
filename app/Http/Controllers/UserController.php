<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\ReconcilePaymongoRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private OrderService $orderService,
    ) {}

    public function showLoginForm()
    {
        return view('users.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $user = Auth::user();
        if (! $user->isAdmin()) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Admin access only. Guests can order without an account.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function index()
    {
        return view('users.index', [
            'users' => $this->userService->listAll(),
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->userService->update($user, $request->validated());
        } catch (ValidationException $e) {
            return redirect()->route('users.index')->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->delete($user, Auth::id());
        } catch (ValidationException $e) {
            return redirect()->route('users.index')->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function ordersKanban()
    {
        return view('users.orders-kanban', [
            'columns' => $this->orderService->kanbanColumns(),
        ]);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        $data = $request->validated();
        $order = $this->orderService->findOrFail((int) $data['order_id']);
        $this->orderService->updateKitchenStatus($order, $data['status']);

        return redirect()->route('admin.orders')->with('success', 'Order status updated.');
    }

    public function reconcilePaymongo(ReconcilePaymongoRequest $request)
    {
        $data = $request->validated();
        $order = $this->orderService->findOrFail((int) $data['order_id']);
        $result = $this->orderService->reconcilePaymongoOrder($order);

        if (! ($result['success'] ?? false)) {
            return redirect()->route('admin.orders')->with('error', $result['error'] ?? 'Reconcile failed.');
        }

        return redirect()->route('admin.orders')->with('success', $result['message'] ?? 'Payment refreshed from PayMongo.');
    }
}
