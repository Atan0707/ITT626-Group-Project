@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Add New Staff Member</h2>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-link">Back to List</a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.staff.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                        id="username" name="username" value="{{ old('username') }}" required>
                    <small class="form-text text-muted">This will be used for staff login</small>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                        id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                        id="password" name="password" required>
                    <small class="form-text text-muted">Minimum 4 characters</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                        id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="shop_id" class="form-label">Assign to Shop</label>
                    <select class="form-control @error('shop_id') is-invalid @enderror" 
                        id="shop_id" name="shop_id" required>
                        <option value="">Select a shop</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('shop_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" 
                            name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active Status</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Staff Member</button>
            </form>
        </div>
    </div>
</div>
@endsection 