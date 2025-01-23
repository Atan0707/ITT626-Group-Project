@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shops</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.shops.create') }}" class="btn btn-primary">
                            Add New Shop
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shops as $shop)
                            <tr>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->address }}</td>
                                <td>
                                    <span class="badge badge-{{ $shop->is_active ? 'success' : 'danger' }}">
                                        {{ $shop->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-sm btn-info">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.shops.destroy', $shop) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection