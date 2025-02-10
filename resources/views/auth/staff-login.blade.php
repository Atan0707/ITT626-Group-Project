@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Admin Login</a>
                            <a href="{{ route('staff.login') }}" class="btn btn-primary">Staff Login</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.login') }}" class="text-center">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            Login as Staff
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 