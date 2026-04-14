@extends('ordering.layout')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="kfc-heading">Menu Management</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.menu.create') }}" class="btn btn-kfc">+ Add New Item</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-kfc">Back to Dashboard</a>
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
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ $item->category }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ Str::limit($item->description, 50) }}</td>
                    <td>
                        @if($item->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.menu.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No menu items found. <a href="{{ route('admin.menu.create') }}">Create one now</a></td>
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
