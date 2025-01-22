@extends('layouts.app')

@section('content')
<style>
    /* Custom pagination styling */
    .pagination {
        margin-bottom: 0;
    }
    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .pagination-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            All Parcels
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
                                    <th style="width: 80px">No #</th>
                                    <th>Tracking Number</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $item)
                                    @if(isset($item->is_date_header) && $item->is_date_header)
                                        <tr class="table-light">
                                            <td colspan="7" class="fw-bold">
                                                {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="text-center fw-bold">#{{ $item->daily_number }}</td>
                                            <td>{{ $item->tracking_number }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->phone_number }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->delivery_date)->format('d M Y') }}</td>
                                            <td>
                                                @if($item->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-success">Collected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.packages.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                                    @if($item->status === 'pending')
                                                        <form action="{{ route('admin.packages.mark-collected', $item) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Mark Collected</button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('admin.packages.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
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

                    @if($packages->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="pagination-text">
                                @if($packages->total() > 0)
                                    @php
                                        $actualCount = $packages->filter(function($item) {
                                            return !isset($item->is_date_header);
                                        })->count();
                                        $totalCount = $packages->total() - $packages->filter(function($item) {
                                            return isset($item->is_date_header);
                                        })->count();
                                    @endphp
                                    Showing {{ max(1, $actualCount) }} to {{ min($totalCount, $packages->perPage()) }} of {{ $totalCount }} packages
                                @else
                                    No packages found
                                @endif
                            </div>
                            <div class="d-flex align-items-center">
                                <nav>
                                    {{ $packages->links('pagination::bootstrap-4') }}
                                </nav>
                            </div>
                        </div>
                    @endif
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