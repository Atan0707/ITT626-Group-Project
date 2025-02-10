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
                                    <a href="{{ route('admin.packages.print', ['date' => request('date')]) }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                                        Print List
                                    </a>
                                </span>
                            @endif
                        </h5>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Add Single Parcel</a>
                        <a href="{{ route('admin.packages.bulk-create') }}" class="btn btn-success">Add Multiple Parcels</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {!! session('success') !!}
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
                                    <th>Discard Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedPackages as $item)
                                    @if(isset($item->is_date_header) && $item->is_date_header)
                                        <tr class="table-light">
                                            <td colspan="8" class="fw-bold">
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
                                            <td>{{ \Carbon\Carbon::parse($item->delivery_date)->addWeek()->format('d M Y') }}</td>
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
                                        <td colspan="8" class="text-center">
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

                    @if($paginatedPackages->hasPages() && !$filterDate)
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="pagination-text">
                                @php
                                    $from = ($paginatedPackages->currentPage() - 1) * $paginatedPackages->perPage() + 1;
                                    $to = min($from + $paginatedPackages->perPage() - 1, $paginatedPackages->total());
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ $paginatedPackages->total() }} packages
                            </div>
                            <div class="d-flex align-items-center">
                                <nav>
                                    {{ $paginatedPackages->links('pagination::bootstrap-4') }}
                                </nav>
                            </div>
                        </div>
                    @elseif(!$filterDate)
                        <div class="d-flex justify-content-end align-items-center mt-4">
                            <div class="pagination-text">
                                Showing {{ $paginatedPackages->total() }} packages
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
                    <h4 class="mb-3">{!! session('success') !!}</h4>
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

async function sendCollectionMessage(event, phoneNumber, name, trackingNumber) {
    if (!confirm('Are you sure you want to mark this package as collected?')) {
        event.preventDefault();
        return;
    }

    try {
        // Format phone number to include +60 if it doesn't already
        const formattedPhone = phoneNumber.startsWith('+60') ? phoneNumber : '+60' + phoneNumber.replace(/^0+/, '');
        
        // Prepare message
        const message = `Dear ${name},\n\nYour parcel with tracking number ${trackingNumber} has been collected.\n\nThank you for using our service!`;

        // Send message via the Node.js server
        const response = await fetch('http://localhost:3000/receive-parcel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                phoneNumber: formattedPhone,
                message: message
            })
        });

        if (!response.ok) {
            console.error('Failed to send Telegram message');
        }
    } catch (error) {
        console.error('Error sending Telegram message:', error);
    }
}
</script>
@endif

@endsection 