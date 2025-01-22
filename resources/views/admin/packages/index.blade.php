@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            Manage Parcels
                            @if($filterDate)
                                <span class="text-muted fs-6 ms-2">
                                    Showing packages for {{ $filterDate }}
                                    <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                        Clear filter
                                    </a>
                                </span>
                            @endif
                        </h5>
                    </div>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Add New Parcel</a>
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
                                    <th style="width: 80px">Daily #</th>
                                    <th>Tracking Number</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentDate = null;
                                @endphp
                                @forelse($packages as $package)
                                    @php
                                        $packageDate = $package->created_at->format('Y-m-d');
                                        $showDate = $currentDate !== $packageDate;
                                        $currentDate = $packageDate;
                                    @endphp
                                    @if($showDate)
                                        <tr class="table-light">
                                            <td colspan="7" class="fw-bold">
                                                {{ $package->created_at->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-center fw-bold">#{{ $package->dailyNumber }}</td>
                                        <td>{{ $package->tracking_number }}</td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->phone_number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($package->delivery_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($package->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-success">Collected</span>
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
                                        <td colspan="7" class="text-center">
                                            @if($filterDate)
                                                No packages found for {{ $filterDate }}
                                            @else
                                                No packages found
                                            @endif
                                        </td>
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

@if(session('success'))
<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">Package Added Successfully</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h4 class="mb-3">{{ session('success') }}</h4>
                    @if(session('dailyNumber'))
                        <div class="alert alert-info">
                            <strong>Today's sorting number: #{{ session('dailyNumber') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
});
</script>
@endif

@endsection 