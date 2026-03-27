@extends('ordering.layout')

@section('title', 'Order Failed - KFC')

@section('content')

<div class="row min-vh-100 align-items-center">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-lg border-0">
            <div class="card-body text-center p-5">
                <!-- Error Icon -->
                <div class="mb-4">
                    <div class="error-emoji">❌</div>
                </div>

                <!-- Error Message -->
                <h1 class="error-title fw-bold mb-3">
                    Order Processing Failed
                </h1>
                <p class="text-muted mb-4 fs-5">
                    Unfortunately, we encountered an issue while processing your order.
                </p>

                <!-- Error Details -->
                <div class="alert alert-danger border-0 mb-4 error-details">
                    <h5 class="error-text mb-3">⚠️ Error Details</h5>
                    <p class="error-text fw-normal fs-6">
                        {{ $errorMessage }}
                    </p>
                </div>

                <!-- Troubleshooting Section -->
                <div class="card border-0 bg-light troubleshooting">
                    <div class="card-body">
                        <h5 class="kfc-text-red mb-3">What You Can Do:</h5>
                        <ol class="text-start small">
                            <li><strong>Check your internet connection</strong> - Ensure you have a stable connection</li>
                            <li><strong>Verify payment details</strong> - Make sure your payment information is correct</li>
                            <li><strong>Try again</strong> - Return to cart and attempt the order once more</li>
                            <li><strong>Contact support</strong> - Our team is ready to help you</li>
                        </ol>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="alert alert-warning border-0 mb-4 warning-note">
                    <h6 class="warning-text mb-3">📏 Important Note</h6>
                    <p class="small mb-0 warning-text">
                        Your cart has been saved. You can continue shopping or complete your order later. 
                        No charges have been made to your account.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 mb-4">
                    <a href="{{ route('ordering.cart', ['mode' => $mode]) }}" class="btn btn-kfc btn-lg">
                        Return to Cart
                    </a>
                    <a href="{{ route('ordering.menu', ['mode' => $mode]) }}" class="btn btn-outline-secondary">
                        Continue Shopping
                    </a>
                    <a href="{{ route('ordering.selection') }}" class="btn btn-outline-secondary">
                        Start New Order
                    </a>
                </div>

                <!-- Support Section -->
                <div class="card border-0 bg-light support-section">
                    <div class="card-body">
                        <h6 class="kfc-text-red mb-3">📞 Need Help?</h6>
                        <p class="small mb-2">Contact our customer support team:</p>
                        <p class="small mb-0">
                            📞 1-800-KFC-FOOD<br>
                            📧 support@kfc.com<br>
                            💬 Live Chat Available 24/7
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .kfc-text-red {
        color: var(--kfc-red);
    }

    .error-emoji {
        font-size: 5rem;
        animation: shake 0.5s;
    }

    .error-title {
        color: #dc3545;
    }

    .error-details {
        background-color: #f8d7da;
    }

    .error-text {
        color: #721c24;
    }

    .warning-note {
        background-color: #fff3cd;
    }

    .warning-text {
        color: #856404;
    }

    .troubleshooting {
        margin-bottom: 1rem;
    }

    .support-section {
        margin-bottom: 0;
    }

    @keyframes shake {
        0%, 100% {
            transform: translateX(0);
        }
        10%, 30%, 50%, 70%, 90% {
            transform: translateX(-5px);
        }
        20%, 40%, 60%, 80% {
            transform: translateX(5px);
        }
    }

    .min-vh-100 {
        min-height: 100vh;
    }
</style>

@endsection
