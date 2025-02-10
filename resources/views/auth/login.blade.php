@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Login</h4>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="login_type" id="admin_login" value="admin" checked>
                            <label class="btn btn-outline-primary" for="admin_login">Admin Login</label>

                            <input type="radio" class="btn-check" name="login_type" id="staff_login" value="staff">
                            <label class="btn btn-outline-primary" for="staff_login">Staff Login</label>
                        </div>
                    </div>

                    <!-- Admin Login Form -->
                    <form id="adminLoginForm" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="admin_username" class="col-md-4 col-form-label text-md-right">Username</label>
                            <div class="col-md-6">
                                <input id="admin_username" type="text" class="form-control @error('username') is-invalid @enderror" 
                                    name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="admin_password" class="col-md-4 col-form-label text-md-right">Password</label>
                            <div class="col-md-6">
                                <input id="admin_password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Login as Admin
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Staff Login Form -->
                    <form id="staffLoginForm" method="POST" action="{{ route('staff.login') }}" style="display: none;">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="staff_username" class="col-md-4 col-form-label text-md-right">Username</label>
                            <div class="col-md-6">
                                <input id="staff_username" type="text" class="form-control @error('username') is-invalid @enderror" 
                                    name="username" value="{{ old('username') }}" required autocomplete="username">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="staff_password" class="col-md-4 col-form-label text-md-right">Password</label>
                            <div class="col-md-6">
                                <input id="staff_password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Login as Staff
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminForm = document.getElementById('adminLoginForm');
    const staffForm = document.getElementById('staffLoginForm');
    const radioButtons = document.querySelectorAll('input[name="login_type"]');

    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'admin') {
                adminForm.style.display = 'block';
                staffForm.style.display = 'none';
            } else {
                adminForm.style.display = 'none';
                staffForm.style.display = 'block';
            }
        });
    });
});
</script>
@endpush
@endsection
