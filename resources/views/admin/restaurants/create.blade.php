@extends('ordering.layout')

@section('title', 'Add Store - Admin')

@section('content')
<h1 class="page-title">Add Store</h1>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.restaurants.store') }}" class="card">
    @csrf
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label" for="name">Store name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="lat">Latitude</label>
                <input type="text" class="form-control" id="lat" name="lat" value="{{ old('lat') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" for="lng">Longitude</label>
                <input type="text" class="form-control" id="lng" name="lng" value="{{ old('lng') }}" required>
            </div>
        </div>
        <div class="mb-3">
            <div id="admin-store-map" style="height: 320px; border: 3px solid var(--kfc-black); border-radius: 12px;"></div>
            <small class="text-muted">Click the map (or drag the pin) to set coordinates.</small>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
            <label class="form-check-label" for="is_active">Active (shown to delivery customers)</label>
        </div>
        <button type="submit" class="btn btn-kfc">Save Store</button>
        <a href="{{ route('admin.restaurants.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection

@section('extra-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
@endsection

@section('extra-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" onload="window.initAdminStoreMap && window.initAdminStoreMap()"></script>
<script src="{{ asset('js/admin-store-map.js') }}"></script>
@endsection
