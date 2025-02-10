@extends('staff.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="mb-4">Staff Dashboard</h2>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Packages</h5>
                            <h2 class="card-text">{{ $stats['total_packages'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending Packages</h5>
                            <h2 class="card-text">{{ $stats['pending_packages'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Collected Packages</h5>
                            <h2 class="card-text">{{ $stats['collected_packages'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Discarded Packages</h5>
                            <h2 class="card-text">{{ $stats['discarded_packages'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Packages -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Packages</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tracking Number</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_packages as $package)
                                    <tr>
                                        <td>{{ $package->tracking_number }}</td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->phone_number }}</td>
                                        <td>{{ $package->delivery_date->format('d M Y') }}</td>
                                        <td>
                                            @if($package->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($package->status === 'collected')
                                                <span class="badge bg-success">Collected</span>
                                            @else
                                                <span class="badge bg-danger">Discarded</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($package->status === 'pending')
                                                <form action="{{ route('staff.packages.mark-collected', $package) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Mark Collected</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No packages found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 