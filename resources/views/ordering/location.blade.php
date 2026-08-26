@extends('ordering.layout')

@section('title', 'Choose Delivery Store - KFC')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <h1 class="page-title">Delivery Location</h1>
        <p class="text-muted">Pin where you are, then pick a KFC store to deliver from.</p>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div id="kiosk-map" style="height: 420px; border: 3px solid var(--kfc-black); border-radius: 16px;"></div>
        <div class="mt-3 d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-kfc" id="use-my-location-btn">Use My Location</button>
            <span class="text-muted small align-self-center" id="location-status">Click the map or use GPS to set your pin.</span>
        </div>
    </div>
    <div class="col-lg-5">
        <form method="POST" action="{{ route('ordering.location.save') }}" id="location-form">
            @csrf
            <input type="hidden" name="mode" value="delivery">
            <input type="hidden" name="customer_lat" id="customer_lat" required>
            <input type="hidden" name="customer_lng" id="customer_lng" required>
            <input type="hidden" name="restaurant_id" id="restaurant_id" required>

            <h5 class="kfc-heading mb-3">Nearby Stores</h5>
            <div id="store-list" class="list-group mb-3">
                <div class="list-group-item text-muted">Set your location to see nearby stores.</div>
            </div>

            <button type="submit" class="btn btn-kfc w-100" id="continue-btn" disabled>Continue to Menu</button>
        </form>
    </div>
</div>
@endsection

@section('extra-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
@endsection

@section('extra-js')
<script>
    window.KIOSK_MAP = {
        storesUrl: @json(route('ordering.nearby')),
        initialStores: @json($stores),
    };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" onload="window.initKioskMap && window.initKioskMap()"></script>
<script src="{{ asset('js/kiosk-map.js') }}"></script>
@endsection
