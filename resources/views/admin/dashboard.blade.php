@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="mb-4">Admin Dashboard</h2>
            
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
                <!-- <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <h2 class="card-text">{{ $stats['total_students'] }}</h2>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Recent Packages -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Packages</h5>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Add New Package</a>
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
                                    <th>Discard Date</th>
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
                                        <td>{{ \Carbon\Carbon::parse($package->delivery_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($package->delivery_date)->addWeek()->format('d M Y') }}</td>
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
                                            <div class="btn-group">
                                                <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-primary">Edit</a>
                                                @if($package->status === 'pending')
                                                    <form action="{{ route('admin.packages.mark-collected', $package) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Mark Collected</button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
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