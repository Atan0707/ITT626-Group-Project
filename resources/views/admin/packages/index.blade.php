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
                                    <th>Tracking Number</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    <tr>
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
                                        <td colspan="6" class="text-center">
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
@endsection 