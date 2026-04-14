<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	public function showLoginForm()
	{
		return view('users.login');
	}

	public function login(Request $request)
	{
		$credentials = $request->validate([
			'email' => ['required', 'email'],
			'password' => ['required', 'string'],
		]);

		if (!Auth::attempt($credentials)) {
			return back()->withErrors([
				'email' => 'The provided credentials do not match our records.',
			])->onlyInput('email');
		}

		$request->session()->regenerate();

		$user = Auth::user();
		if ($user->isAdmin()) {
			return redirect()->route('admin.dashboard');
		} else {
			return redirect()->route('ordering.selection');
		}
	}

	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect()->route('login');
	}

	public function showRegisterForm()
	{
		return view('users.register');
	}

	public function register(Request $request)
	{
		$data = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'max:255', 'unique:users,email'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
		]);

		$user = User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => Hash::make($data['password']),
			'role' => 'customer',
		]);

		Auth::login($user);
		$request->session()->regenerate();

		return redirect()->route('ordering.selection')->with('success', 'Account created successfully.');
	}

	public function index()
	{
		$users = User::orderBy('id')->get();

		return view('users.index', [
			'users' => $users,
		]);
	}

	public function create()
	{
		return view('users.create');
	}

	public function store(Request $request)
	{
		$data = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'max:255', 'unique:users,email'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
			'role' => ['required', Rule::in(['customer', 'admin'])],
		]);

		User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => Hash::make($data['password']),
			'role' => $data['role'],
		]);

		return redirect()->route('users.index')->with('success', 'User created successfully.');
	}

	public function edit(User $user)
	{
		return view('users.edit', [
			'user' => $user,
		]);
	}

	public function update(Request $request, User $user)
	{
		$data = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
			'password' => ['nullable', 'string', 'min:8', 'confirmed'],
			'role' => ['required', Rule::in(['customer', 'admin'])],
		]);

		$updatePayload = [
			'name' => $data['name'],
			'email' => $data['email'],
			'role' => $data['role'],
		];

		if ($user->role === 'admin' && $data['role'] === 'customer' && User::where('role', 'admin')->count() <= 1) {
			return redirect()->route('users.index')->with('error', 'At least one admin account must remain in the system.');
		}

		if (!empty($data['password'])) {
			$updatePayload['password'] = Hash::make($data['password']);
		}

		$user->update($updatePayload);

		return redirect()->route('users.index')->with('success', 'User updated successfully.');
	}

	public function destroy(User $user)
	{
		if (Auth::id() === $user->id) {
			return redirect()->route('users.index')->with('error', 'You cannot delete your own account while logged in.');
		}

		if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
			return redirect()->route('users.index')->with('error', 'At least one admin account must remain in the system.');
		}

		$user->delete();

		return redirect()->route('users.index')->with('success', 'User deleted successfully.');
	}

	public function ordersKanban()
	{
		$columns = [
			'placed' => Order::where('status', 'placed')->with('user')->get(),
			'preparing' => Order::where('status', 'preparing')->with('user')->get(),
			'ready' => Order::where('status', 'ready')->with('user')->get(),
			'completed' => Order::where('status', 'completed')->with('user')->get(),
			'cancelled' => Order::where('status', 'cancelled')->with('user')->get(),
		];

		return view('users.orders-kanban', [
			'columns' => $columns,
		]);
	}

	public function updateOrderStatus(Request $request)
	{
		$data = $request->validate([
			'order_id' => ['required', 'exists:orders,id'],
			'status' => ['required', Rule::in(['placed', 'preparing', 'ready', 'completed', 'cancelled'])],
		]);

		$order = Order::findOrFail($data['order_id']);
		$order->update(['status' => $data['status']]);

		return redirect()->route('admin.orders')->with('success', 'Order status updated.');
	}
}
