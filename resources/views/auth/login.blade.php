@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ __('Login') }}</h4>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="loginTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">Student Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">Admin Login</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="loginTabsContent">
                        <!-- Student Login Form -->
                        <div class="tab-pane fade show active" id="student" role="tabpanel">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <input type="hidden" name="role" value="student">

                                <div class="row mb-3">
                                    <label for="student_id" class="col-md-4 col-form-label text-md-end">{{ __('Student ID') }}</label>
                                    <div class="col-md-6">
                                        <input id="student_id" type="text" class="form-control @error('student_id') is-invalid @enderror" name="student_id" value="{{ old('student_id') }}" required autofocus>
                                        @error('student_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Login') }}
                                        </button>
                                        <a href="{{ route('register') }}" class="btn btn-link">
                                            {{ __('New Student? Register here') }}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Admin Login Form -->
                        <div class="tab-pane fade" id="admin" role="tabpanel">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <input type="hidden" name="role" value="admin">

                                <div class="row mb-3">
                                    <label for="admin_username" class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>
                                    <div class="col-md-6">
                                        <input id="admin_username" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="admin" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="admin_password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                                    <div class="col-md-6">
                                        <input id="admin_password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Login as Admin') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
}
.nav-tabs .nav-link.active {
    color: #0d6efd;
    font-weight: 600;
}
.card {
    border: none;
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
}
.btn-primary {
    padding: 8px 20px;
}
</style>
@endsection
