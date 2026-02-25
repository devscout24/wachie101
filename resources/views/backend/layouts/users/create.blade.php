@extends('backend.master')

@section('title')
    Users - Create
@endsection

@section('body')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Users Create</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <div class="col-lg-6 col-md-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h3 class="card-title">Create New User</h3>
            </div>

            <div class="card-body">
                <p class="text-muted">Fill the form to create a new user.</p>

                {{-- Show validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="form-horizontal" method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="row mb-4">
                        <label for="name" class="col-md-3 form-label">Name</label>
                        <div class="col-md-9">
                            <input
                                type="text" id="name" name="name" class="form-control" placeholder="Enter full name" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="row mb-4">
                        <label for="email" class="col-md-3 form-label">Email</label>
                        <div class="col-md-9">
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="row mb-4">
                        <label for="password" class="col-md-3 form-label">Password</label>
                        <div class="col-md-9">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="row mb-4">
                        <label for="password_confirmation" class="col-md-3 form-label">Confirm Password</label>
                        <div class="col-md-9">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
