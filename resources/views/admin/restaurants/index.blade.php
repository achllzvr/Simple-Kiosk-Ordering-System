@extends('ordering.layout')

@section('title', 'Stores - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="page-title mb-0">Store Locations</h1>
    <a href="{{ route('admin.restaurants.create') }}" class="btn btn-kfc">Add Store</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Coordinates</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($restaurants as $restaurant)
                    <tr>
                        <td>{{ $restaurant->name }}</td>
                        <td>{{ $restaurant->address }}</td>
                        <td><code>{{ $restaurant->lat }}, {{ $restaurant->lng }}</code></td>
                        <td>{{ $restaurant->is_active ? 'Yes' : 'No' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="btn btn-sm btn-kfc-outline">Edit</a>
                            <form action="{{ route('admin.restaurants.destroy', $restaurant) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this store?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">No stores yet. Seed or create one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
