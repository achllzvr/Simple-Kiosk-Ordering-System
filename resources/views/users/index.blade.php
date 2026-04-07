@extends('ordering.layout')

@section('title', 'User Management - KFC')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h1 class="page-title text-md-start mb-0">User Management</h1>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('users.create') }}" class="btn btn-kfc">+ Add User</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge text-bg-danger">Admin</span>
                                @else
                                    <span class="badge text-bg-secondary">Customer</span>
                                @endif
                            </td>
                            <td>{{ optional($user->created_at)->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-kfc-outline">Edit</a>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline-block" onsubmit="return confirm('Delete this user account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <a href="{{ route('ordering.selection') }}" class="btn btn-kfc">Go to Ordering</a>
    <a href="{{ route('admin.orders') }}" class="btn btn-kfc-outline">View Orders Kanban</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">Logout</button>
    </form>
</div>
@endsection
