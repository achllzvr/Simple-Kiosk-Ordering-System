@extends('ordering.layout')

@section('title', 'Choose Ordering Mode - KFC')

@section('content')
<div class="row min-vh-100 align-items-center">
    <div class="col-12">
        <div class="text-center mb-5">
            <img
                src="{{ asset('assets/images/KFC_Logo_full_text_only.svg') }}"
                alt="KFC"
                class="selection-logo-text"
            >
            <p class="selection-subtitle text-muted">How would you like to order?</p>
        </div>

        <div class="row justify-content-center">
            <!-- Dine-in Option -->
            <div class="col-md-5 mb-4">
                <div class="card h-100 selection-card">
                    <div class="card-body text-center p-5">
                        <div class="selection-emoji">🍽️</div>
                        <h2 class="card-title kfc-text-red fw-bold mb-2">Dine-In</h2>
                        <p class="card-text text-muted mb-4">Enjoy your order fresh inside the restaurant</p>
                        <a href="{{ route('ordering.menu', ['mode' => 'dine-in']) }}" class="btn btn-kfc btn-lg w-100">
                            Order for Dine-In
                        </a>
                    </div>
                </div>
            </div>

            <!-- Take-out Option -->
            <div class="col-md-5 mb-4">
                <div class="card h-100 selection-card">
                    <div class="card-body text-center p-5">
                        <div class="selection-emoji">🏪</div>
                        <h2 class="card-title kfc-text-red fw-bold mb-2">Take-Out</h2>
                        <p class="card-text text-muted mb-4">Pick up your order at our restaurant counter</p>
                        <a href="{{ route('ordering.menu', ['mode' => 'take-out']) }}" class="btn btn-kfc btn-lg w-100">
                            Order for Take-Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .selection-logo-text {
        width: 220px;
        max-width: 100%;
        height: auto;
        margin-bottom: 20px;
    }

    .selection-subtitle {
        font-size: 1.3rem;
        color: #666;
    }

    .selection-emoji {
        font-size: 5rem;
        margin-bottom: 20px;
    }

    .selection-card {
        cursor: pointer;
    }

    .kfc-text-red {
        color: var(--kfc-red);
    }

    .min-vh-100 {
        min-height: 100vh;
    }
</style>
@endsection
