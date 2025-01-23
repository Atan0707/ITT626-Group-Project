@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Shop</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.shops.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shops.update', $shop) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.shops.form')
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Shop</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 