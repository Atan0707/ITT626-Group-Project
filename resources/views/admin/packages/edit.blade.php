@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Package</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.packages.update', $package) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="tracking_number" class="form-label">Tracking Number</label>
                            <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                id="tracking_number" name="tracking_number" 
                                value="{{ old('tracking_number', $package->tracking_number) }}" required>
                            @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select @error('student_id') is-invalid @enderror" 
                                id="student_id" name="student_id" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->student_id }}" 
                                        {{ (old('student_id', $package->student_id) == $student->student_id) ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->student_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sender_name" class="form-label">Sender Name</label>
                            <input type="text" class="form-control @error('sender_name') is-invalid @enderror" 
                                id="sender_name" name="sender_name" 
                                value="{{ old('sender_name', $package->sender_name) }}" required>
                            @error('sender_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="courier_company" class="form-label">Courier Company</label>
                            <input type="text" class="form-control @error('courier_company') is-invalid @enderror" 
                                id="courier_company" name="courier_company" 
                                value="{{ old('courier_company', $package->courier_company) }}" required>
                            @error('courier_company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="arrival_date" class="form-label">Arrival Date</label>
                            <input type="date" class="form-control @error('arrival_date') is-invalid @enderror" 
                                id="arrival_date" name="arrival_date" 
                                value="{{ old('arrival_date', $package->arrival_date->format('Y-m-d')) }}" required>
                            @error('arrival_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes', $package->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Package</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 