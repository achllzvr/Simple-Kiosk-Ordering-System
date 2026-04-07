@extends('ordering.layout')

@section('title', 'Login - KFC')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-5">
        <h1 class="page-title">Login to Continue Ordering</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-kfc w-100 mt-2">Login</button>
                </form>

                <hr>

                <p class="mb-0 text-center">
                    No account yet?
                    <a href="{{ route('register') }}" class="fw-bold text-danger">Create one</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
