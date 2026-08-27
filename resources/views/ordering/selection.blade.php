@extends('ordering.layout')

@section('title', 'Choose Ordering Mode - KFC')

@section('content')
<div class="selection-stage">
    <div class="selection-header">
        <img
            src="{{ asset('assets/images/KFC_Logo_full_text_only.svg') }}"
            alt="KFC"
            class="selection-logo-text"
        >
        <p class="selection-subtitle text-muted mb-0">How would you like to order?</p>
    </div>

    <div class="row justify-content-center selection-grid">
        <div class="col-md-4">
            <div class="card h-100 selection-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="selection-emoji" aria-hidden="true">🍽️</div>
                    <h2 class="card-title kfc-text-red fw-bold mb-2">Dine-In</h2>
                    <p class="card-text text-muted mb-4 flex-grow-1">Enjoy your order fresh inside the restaurant</p>
                    <a href="{{ route('ordering.menu', ['mode' => 'dine-in']) }}" class="btn btn-kfc btn-lg w-100 mt-auto">
                        Order for Dine-In
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 selection-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="selection-emoji" aria-hidden="true">🏪</div>
                    <h2 class="card-title kfc-text-red fw-bold mb-2">Take-Out</h2>
                    <p class="card-text text-muted mb-4 flex-grow-1">Pick up your order at our restaurant counter</p>
                    <a href="{{ route('ordering.menu', ['mode' => 'take-out']) }}" class="btn btn-kfc btn-lg w-100 mt-auto">
                        Order for Take-Out
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 selection-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="selection-emoji" aria-hidden="true">🛵</div>
                    <h2 class="card-title kfc-text-red fw-bold mb-2">Delivery</h2>
                    <p class="card-text text-muted mb-4 flex-grow-1">Pin your location and choose a nearby store</p>
                    <a href="{{ route('ordering.location', ['mode' => 'delivery']) }}" class="btn btn-kfc btn-lg w-100 mt-auto">
                        Order for Delivery
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .selection-logo-text {
        width: min(220px, 70vw);
        height: auto;
        margin: 0 auto 1rem;
        display: block;
    }

    .selection-subtitle {
        font-size: 1.2rem;
        color: #666;
    }

    .selection-emoji {
        font-size: 3.5rem;
        margin-bottom: 0.75rem;
        line-height: 1;
    }

    .selection-card {
        cursor: pointer;
    }

    .kfc-text-red {
        color: var(--kfc-red);
    }
</style>
@endsection
