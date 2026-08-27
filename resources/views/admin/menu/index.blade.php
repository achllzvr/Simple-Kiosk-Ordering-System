@extends('ordering.layout')

@section('title', 'Menu Management - KFC')

@section('content')
<div class="admin-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="page-title text-md-start mb-0">Menu Management</h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.menu.create') }}" class="btn btn-kfc">Add New Item</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-kfc">Back to Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-kfc">
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" width="48" height="48" style="object-fit: cover; border-radius: 8px; border: 2px solid var(--kfc-black);">
                    </td>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ $item->category }}</td>
                    <td>₱{{ number_format($item->price, 2) }}</td>
                    <td>
                        @if($item->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.menu.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" class="d-inline" data-confirm="Delete this menu item?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No menu items found. <a href="{{ route('admin.menu.create') }}">Create one now</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
