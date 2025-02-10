@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Staff Management</h2>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">Add New Staff</a>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Shop</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMembers as $staff)
                            <tr>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->phone_number }}</td>
                                <td>{{ $staff->shop->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $staff->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $staff->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.staff.edit', $staff) }}" 
                                           class="btn btn-sm btn-primary">Edit</a>
                                           
                                        <form action="{{ route('admin.staff.destroy', $staff) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No staff members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if(isset($staffMembers) && method_exists($staffMembers, 'links'))
                    <div class="mt-4">
                        {{ $staffMembers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 