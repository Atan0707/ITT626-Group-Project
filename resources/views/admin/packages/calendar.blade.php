@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Package Dates</h5>
        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Add New Package</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <!-- Month Filter -->
                <div class="mb-4">
                    <form action="{{ route('admin.packages.calendar') }}" method="GET" class="d-flex gap-2">
                        <select name="month" class="form-select" onchange="this.form.submit()">
                            <option value="">Select Month</option>
                            @foreach($months as $month)
                                <option value="{{ $month['value'] }}" {{ $selectedMonth == $month['value'] ? 'selected' : '' }}>
                                    {{ $month['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @if($selectedMonth)
                            <a href="{{ route('admin.packages.calendar') }}" class="btn btn-outline-secondary">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                @if($dates->isNotEmpty())
                    <div class="list-group">
                        @foreach($dates as $date)
                            <a href="{{ route('admin.packages.index') }}?date={{ $date->delivery_date }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                {{ \Carbon\Carbon::parse($date->delivery_date)->format('d M Y') }}
                                <span class="badge bg-primary rounded-pill">{{ $date->count }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        @if($selectedMonth)
                            No packages found for {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}.
                        @else
                            No packages found. Click "Add New Package" to create one.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.gap-2 {
    gap: 0.5rem;
}
</style>
@endsection 