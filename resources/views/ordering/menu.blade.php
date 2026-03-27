@extends('ordering.layout')

@section('title', 'Menu - KFC')

@section('content')

<!-- Search Section -->
<div class="row mb-4 search-section">
    <div class="col-md-8 mx-auto">
        <h1 class="page-title mb-4">Our Menu</h1>
        <div class="input-group input-group-lg">
            <form method="GET" action="{{ route('ordering.menu') }}" class="w-100 d-flex gap-2">
                <input 
                    type="hidden" 
                    name="mode" 
                    value="{{ $mode }}"
                >
                <input 
                    class="form-control" 
                    type="text" 
                    name="search" 
                    placeholder="Search for food items..." 
                    value="{{ $search }}"
                >
                <button class="btn btn-kfc" type="submit">Search</button>
            </form>
        </div>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>✓ Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Menu Items Grid -->
<div class="row g-5 menu-grid">
    @forelse($items as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 menu-card-wrapper">
                <!-- Image -->
                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="menu-item-image card-img-top">
                
                <!-- Card Body -->
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title kfc-text-red fw-bold menu-item-name">{{ $item['name'] }}</h5>
                    <p class="card-text text-muted menu-item-description">{{ $item['description'] }}</p>
                    
                    <!-- Price Badge -->
                    <div class="mt-auto mb-3">
                        <span class="badge badge-price">₱{{ $item['price'] }}</span>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="card-footer bg-white border-top p-3">
                    <button 
                        class="btn btn-kfc w-100" 
                        data-bs-toggle="modal" 
                        data-bs-target="#itemModal{{ $item['id'] }}"
                    >
                        View Details & Add
                    </button>
                </div>
            </div>
        </div>

        <!-- Item Details Modal -->
        <div class="modal fade" id="itemModal{{ $item['id'] }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $item['name'] }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <!-- Item Image -->
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-100 rounded mb-3 modal-item-image">
                        
                        <!-- Description -->
                        <p class="text-muted">{{ $item['description'] }}</p>

                        <!-- Add to Cart Form -->
                        <form method="POST" action="{{ route('add-to-cart') }}">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                            <input type="hidden" name="mode" value="{{ $mode }}">

                            <!-- Variations Section -->
                            @if(count($item['variations']) > 0)
                                <div class="mb-4">
                                    <label class="form-label">Select Variation:</label>
                                    <div class="list-group">
                                        @foreach($item['variations'] as $variation)
                                            <label class="list-group-item">
                                                <input 
                                                    class="form-check-input me-2" 
                                                    type="radio" 
                                                    name="variation" 
                                                    value="{{ $variation['name'] }}"
                                                    @if($loop->first) checked @endif
                                                >
                                                {{ $variation['name'] }}
                                                @if($variation['price'] > 0)
                                                    <span class="badge bid-price ms-2">+₱{{ $variation['price'] }}</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Add-ons Section -->
                            @if(count($item['addons']) > 0)
                                <div class="mb-4">
                                    <label class="form-label">Add-ons (Optional):</label>
                                    <div class="list-group">
                                        @foreach($item['addons'] as $addon)
                                            <label class="list-group-item">
                                                <input 
                                                    class="form-check-input me-2" 
                                                    type="checkbox" 
                                                    name="addons[]" 
                                                    value="{{ $addon['name'] }}"
                                                >
                                                {{ $addon['name'] }}
                                                <span class="badge badge-addon ms-2">+₱{{ $addon['price'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Quantity Section -->
                            <div class="mb-4">
                                <label for="quantity{{ $item['id'] }}" class="form-label">Quantity:</label>
                                <div class="input-group quantity-input-group">
                                    <input 
                                        type="number" 
                                        class="form-control text-center" 
                                        id="quantity{{ $item['id'] }}" 
                                        name="quantity" 
                                        value="1" 
                                        min="1"
                                        max="99"
                                    >
                                </div>
                            </div>

                            <!-- Price Display -->
                            <div class="price-section mb-4">
                                <h5 class="kfc-text-red">Base Price: <span class="float-end">₱{{ $item['price'] }}</span></h5>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-kfc flex-grow-1">
                                    Add to Cart
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center p-5">
                <h5>No items found</h5>
                <p class="mb-0">Try searching for different items or <a href="{{ route('ordering.menu', ['mode' => $mode]) }}">view all items</a></p>
            </div>
        </div>
    @endforelse
</div>

<style>
    .kfc-text-red {
        color: var(--kfc-red);
    }

    .bid-price {
        background-color: var(--kfc-red);
    }

    .modal-item-image {
        height: 250px;
        object-fit: cover;
    }

    .quantity-input-group {
        max-width: 120px;
    }

    .list-group-item {
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .list-group-item input[type="radio"]:checked ~ label,
    .list-group-item input[type="checkbox"]:checked ~ label {
        background-color: #e7f5ff;
    }
</style>

@endsection
