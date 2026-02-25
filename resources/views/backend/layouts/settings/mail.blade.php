@extends('backend.master')

@section('title')
    Admin Mail Setting
@endsection

@section('body')
<!-- PAGE-HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Setting</h1>
    </div>
    <div class="ms-auto pageheader-btn">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">System</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mail Setting</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<div class="col-md-12">
    <div class="card">
        <div class="card-header border-bottom">
            <h3 class="card-title">Mail Setting</h3>
        </div>

        <div class="card-body">
            <p class="text-muted">Setup your system mail, please provide your valid data.</p>

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

            <form class="form-horizontal" method="POST" action="{{ route('settings.mailstore') }}">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="mail_mailer" class="form-label">Mail Mailer</label>
                        <input
                            type="text"
                            id="mail_mailer"
                            name="mail_mailer"
                            class="form-control"
                            placeholder="e.g., smtp"
                            value="{{ old('mail_mailer', config('mail.mailer')) }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="mail_host" class="form-label">Mail Host</label>
                        <input
                            type="text"
                            id="mail_host"
                            name="mail_host"
                            class="form-control"
                            placeholder="e.g., smtp.example.com"
                            value="{{ old('mail_host', config('mail.host')) }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="mail_port" class="form-label">Mail Port</label>
                        <input
                            type="text"
                            id="mail_port"
                            name="mail_port"
                            class="form-control"
                            placeholder="e.g., 587"
                            value="{{ old('mail_port', config('mail.port')) }}"
                            required
                        >
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="mail_username" class="form-label">Mail Username</label>
                        <input
                            type="text"
                            id="mail_username"
                            name="mail_username"
                            class="form-control"
                            placeholder="e.g., user@example.com"
                            value="{{ old('mail_username', config('mail.username')) }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="mail_password" class="form-label">Mail Password</label>
                        <input
                            type="text"
                            id="mail_password"
                            name="mail_password"
                            class="form-control"
                            placeholder="Enter mail password"
                            value="{{ old('mail_password', config('mail.password')) }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="mail_encryption" class="form-label">Mail Encryption</label>
                        <input
                            type="text"
                            id="mail_encryption"
                            name="mail_encryption"
                            class="form-control"
                            placeholder="e.g., tls"
                            value="{{ old('mail_encryption', config('mail.encryption')) }}"
                        >
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="mail_from_address" class="form-label">Mail From Address</label>
                        <input
                            type="text"
                            id="mail_from_address"
                            name="mail_from_address"
                            class="form-control"
                            placeholder="e.g., no-reply@example.com"
                            value="{{ old('mail_from_address', config('mail.from.address')) }}"
                            required
                        >
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
