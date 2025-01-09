@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="mb-4">My Packages</h2>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Packages</h5>
                            <h2 class="card-text">{{ $stats['total_packages'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending Collection</h5>
                            <h2 class="card-text">{{ $stats['pending_packages'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Collected</h5>
                            <h2 class="card-text">{{ $stats['collected_packages'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Packages List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Package History</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tracking Number</th>
                                    <th>Status</th>
                                    <th>Arrival Date</th>
                                    <th>Collection Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    <tr>
                                        <td>{{ $package->tracking_number }}</td>
                                        <td>
                                            @if($package->status === 'pending')
                                                <span class="badge bg-warning">Pending Collection</span>
                                            @else
                                                <span class="badge bg-success">Collected</span>
                                            @endif
                                        </td>
                                        <td>{{ $package->arrival_date->format('d M Y') }}</td>
                                        <td>{{ $package->collection_date ? $package->collection_date->format('d M Y') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No packages found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $packages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 