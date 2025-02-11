@extends('staff.layout_staff.app_staff')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Package Dates</h5>
        <a href="{{ route('staff.packages.create') }}" class="btn btn-primary">Add New Package</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <!-- Month Filter -->
                <div class="mb-4">
                    <form action="{{ route('staff.packages.calendar') }}" method="GET" class="d-flex gap-2">
                        <select name="month" class="form-select" onchange="this.form.submit()">
                            <option value="">All Months</option>
                            @php
                                $currentYear = date('Y');
                                $months = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    $monthValue = $currentYear . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
                                    $monthLabel = date('F Y', strtotime($currentYear . '-' . $m . '-01'));
                                    $months[] = ['value' => $monthValue, 'label' => $monthLabel];
                                }
                            @endphp
                            @foreach($months as $month)
                                <option value="{{ $month['value'] }}" {{ request('month') == $month['value'] ? 'selected' : '' }}>
                                    {{ $month['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('month'))
                            <a href="{{ route('staff.packages.calendar') }}" class="btn btn-outline-secondary">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                @if(isset($dates) && $dates->isNotEmpty())
                    <div class="list-group">
                        @foreach($dates as $date)
                            <a href="{{ route('staff.packages.index') }}?filter_date={{ $date->delivery_date }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                {{ \Carbon\Carbon::parse($date->delivery_date)->format('d M Y') }}
                                <span class="badge bg-primary rounded-pill">{{ $date->count }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        @if(request('month'))
                            No packages found for {{ \Carbon\Carbon::parse(request('month') . '-01')->format('F Y') }}.
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