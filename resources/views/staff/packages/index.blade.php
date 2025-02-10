@extends('staff.layout_staff.app_staff')

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
    /* Updated scrolling styles */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow-y: scroll;
    }
    .content-wrapper {
        min-height: 100%;
        padding: 1rem;
        position: relative;
    }
    .table-container {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 1rem;
    }
    .card {
        margin-bottom: 1rem;
    }
    /* Header and button styles */
    .card-header {
        padding: 1rem;
        background-color: #f8f9fa;
    }
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .header-title {
        margin: 0;
        flex: 1;
    }
    .header-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: nowrap;
    }
    .header-buttons .btn {
        white-space: nowrap;
    }
    /* Button group fixes */
    .btn-group {
        display: flex;
        gap: 0.25rem;
    }
    .btn-group .btn {
        white-space: nowrap;
    }
    /* Make table more compact on mobile */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            align-items: stretch;
        }
        .header-buttons {
            flex-direction: column;
        }
        .header-buttons .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        .table td, .table th {
            padding: 0.5rem;
        }
        .btn-group {
            flex-direction: column;
            align-items: stretch;
        }
        .btn-group .btn {
            margin-bottom: 0.25rem;
        }
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="header-content">
                            <div class="header-title">
                                <h5 class="mb-0">
                                    All Parcels
                                    @if(request('date'))
                                        <span class="text-muted fs-6 ms-2">
                                            Showing packages for {{ request('date') }}
                                            <a href="{{ route('staff.packages.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                                Clear filter
                                            </a>
                                            <a href="{{ route('staff.packages.print', ['date' => request('date')]) }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                                                Print List
                                            </a>
                                        </span>
                                    @endif
                                </h5>
                            </div>
                            <div class="header-buttons">
                                <a href="{{ route('staff.packages.create') }}" class="btn btn-primary">Add Single Parcel</a>
                                <a href="{{ route('staff.packages.bulk-create') }}" class="btn btn-success">Add Multiple Parcels</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {!! session('success') !!}
                            </div>
                        @endif

                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No #</th>
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
                                    @forelse($packages as $package)
                                        <tr>
                                            <td class="text-center fw-bold">#{{ $package->daily_number }}</td>
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
                                                    <a href="{{ route('staff.packages.edit', $package) }}" class="btn btn-sm btn-primary">Edit</a>
                                                    @if($package->status === 'pending')
                                                        <form action="{{ route('staff.packages.mark-collected', $package) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Mark Collected</button>
                                                        </form>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-info send-reminder" 
                                                                data-package-id="{{ $package->id }}"
                                                                onclick="sendReminder(this)">
                                                            Reminder
                                                        </button>
                                                    @endif
                                                    <form action="{{ route('staff.packages.destroy', $package) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this package?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                @if(request('date'))
                                                    No packages found for {{ request('date') }}
                                                @else
                                                    No packages found
                                                @endif
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($packages, 'hasPages') && $packages->hasPages() && !request('date'))
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="pagination-text">
                                    Showing {{ $packages->firstItem() }} to {{ $packages->lastItem() }} of {{ $packages->total() }} packages
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
    var successModal = document.getElementById('successModal');
    if (successModal) {
        var modal = new bootstrap.Modal(successModal);
        modal.show();
    }
});

async function sendReminder(button) {
    const packageId = button.dataset.packageId;
    button.disabled = true;
    
    try {
        const response = await fetch(`/staff/packages/${packageId}/send-reminder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        // Show alert based on response
        const alertClass = data.success ? 'success' : 'danger';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert alert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);
        
        // Remove alert after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
            button.disabled = false;
        }, 3000);
    } catch (error) {
        console.error('Error sending reminder:', error);
        button.disabled = false;
    }
}
</script>
@endif
@endsection 