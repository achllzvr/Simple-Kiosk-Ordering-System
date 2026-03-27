@extends('ordering.layout')

@section('title', 'Order Successful - KFC')

@section('content')

<div class="row align-items-start success-page-row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-body text-center p-5">
                <!-- Success Icon -->
                <div class="mb-4">
                    <div class="success-emoji">✅</div>
                </div>

                <!-- Success Message -->
                <h1 class="kfc-text-red fw-bold mb-3">
                    Order Placed Successfully!
                </h1>
                <p class="text-muted mb-4 fs-5">
                    Thank you for your order. Your delicious meal is being prepared.
                </p>

                <!-- Order ID -->
                <div class="alert alert-success mb-4 success-order-id">
                    <h5 class="success-text mb-3">Order ID</h5>
                    <h3 class="success-text fw-bold order-id-text">{{ $orderId }}</h3>
                    <p class="small mb-0 success-text">Please save this ID for your records</p>
                </div>

                <!-- Order Status -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card status-card h-100">
                            <div class="card-body">
                                <h6 class="kfc-text-red mb-3">📦 Status</h6>
                                <p class="fs-5 fw-bold">Order Received</p>
                                <p class="small text-muted mb-0">Being prepared in our kitchen</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card status-card h-100">
                            <div class="card-body">
                                <h6 class="kfc-text-red mb-3">⏱️ Estimated Time</h6>
                                <p class="fs-5 fw-bold">30-45 mins</p>
                                <p class="small text-muted mb-0">Your order will be ready soon</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 mb-4">
                    <a href="{{ route('ordering.selection') }}" class="btn btn-kfc btn-lg">
                        Place Another Order
                    </a>
                    <a href="{{ route('ordering.selection') }}" class="btn btn-outline-secondary">
                        Back to Home
                    </a>
                </div>

                <!-- Contact Information -->
                <div class="text-muted small">
                    <p>
                        Need help? Contact us at<br>
                        📞 1-800-KFC-FOOD | 📧 order@kfc.com
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .kfc-text-red {
        color: var(--kfc-red);
    }

    .success-emoji {
        font-size: 5rem;
        animation: bounce 1s;
    }

    .success-order-id {
        background-color: #d4edda;
    }

    .success-page-row {
        margin-top: 0;
        padding-top: 0;
    }

    .status-card {
        background-color: #fff;
    }

    .success-text {
        color: #155724;
    }

    .order-id-text {
        letter-spacing: 2px;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

</style>

@endsection
