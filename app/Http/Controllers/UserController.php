<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	private function getDummyDataPath(string $fileName): string
	{
		return base_path('dummy data/' . $fileName);
	}

	private function readJsonFile(string $fileName, array $fallback = []): array
	{
		$path = $this->getDummyDataPath($fileName);

		if (!file_exists($path)) {
			file_put_contents($path, json_encode($fallback, JSON_PRETTY_PRINT));
			return $fallback;
		}

		$content = file_get_contents($path);
		$decoded = json_decode($content, true);

		return is_array($decoded) ? $decoded : $fallback;
	}

	private function writeJsonFile(string $fileName, array $data): void
	{
		$path = $this->getDummyDataPath($fileName);
		file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
	}

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

		return redirect()->intended(route('ordering.selection'));
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
		$orders = $this->readJsonFile('orders.json', []);
		$columns = [
			'placed' => [],
			'preparing' => [],
			'ready' => [],
			'completed' => [],
			'cancelled' => [],
		];

		foreach ($orders as $order) {
			$status = $order['status'] ?? 'placed';
			if (!array_key_exists($status, $columns)) {
				$status = 'placed';
			}
			$columns[$status][] = $order;
		}

		return view('users.orders-kanban', [
			'columns' => $columns,
		]);
	}

	public function updateOrderStatus(Request $request)
	{
		$data = $request->validate([
			'order_id' => ['required', 'string'],
			'status' => ['required', Rule::in(['placed', 'preparing', 'ready', 'completed', 'cancelled'])],
		]);

		$orders = $this->readJsonFile('orders.json', []);
		$updated = false;

		foreach ($orders as &$order) {
			if (($order['order_id'] ?? '') === $data['order_id']) {
				$order['status'] = $data['status'];
				$updated = true;
				break;
			}
		}
		unset($order);

		if (!$updated) {
			return redirect()->route('admin.orders')->with('error', 'Order not found.');
		}

		$this->writeJsonFile('orders.json', $orders);

		return redirect()->route('admin.orders')->with('success', 'Order status updated.');
	}
}
