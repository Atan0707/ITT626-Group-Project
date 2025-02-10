@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Package Calendar</h3>
        </div>
        <div class="card-body">
            @foreach($packagesByMonth as $yearMonth => $monthPackages)
                @php
                    $date = \Carbon\Carbon::createFromFormat('Y-m', $yearMonth);
                    $monthName = $months[$date->month];
                    $year = $date->year;
                @endphp
                
                <h4>{{ $monthName }} {{ $year }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <!-- Your table content here -->
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection 