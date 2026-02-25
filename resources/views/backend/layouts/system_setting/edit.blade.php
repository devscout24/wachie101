@extends('backend.master')

@section('title')
    System Settings
@endsection

@section('body')
    <div class="page-header">
        <div>
            <h1 class="page-title">System Settings</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                <li class="breadcrumb-item active" aria-current="page">System Settings</li>
            </ol>
        </div>
    </div>

    <div class="page">
        <div class="page-main">
            <div class="row">
                <div class="col-md-12">

                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">System Settings</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('system-settings.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    {{-- System Logo --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="system_logo" class="form-label">System Logo</label>

                                        {{-- Preview current logo --}}
                                        @if ($settings->system_logo)
                                            <div class="mb-2">
                                                <img src="{{ asset($settings->system_logo) }}?v={{ time() }}"
                                                    alt="System Logo" class="img-thumbnail" style="height: 60px;">
                                            </div>
                                        @endif

                                        <input type="file" name="system_logo" id="system_logo"
                                            class="form-control @error('system_logo') is-invalid @enderror"
                                            accept="image/jpeg,image/jpg,image/png,image/svg+xml">

                                        @error('system_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <small class="form-text text-muted">
                                            Accepted formats: JPG, PNG, SVG. Max size: 2MB
                                        </small>
                                    </div>


                                    {{-- System Favicon --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="system_favicon" class="form-label">System Favicon</label>
                                        @if ($settings->system_favicon_url)
                                            <div class="mb-2">
                                                <img src="{{ $settings->system_favicon_url }}" alt="System Favicon"
                                                    class="img-thumbnail" style="height: 40px;">
                                            </div>
                                        @endif
                                        <input type="file" name="system_favicon" id="system_favicon"
                                            class="form-control @error('system_favicon') is-invalid @enderror"
                                            accept="image/png,image/ico,image/jpeg,image/jpg">
                                        @error('system_favicon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Accepted formats: PNG, ICO, JPG. Max size:
                                            1MB</small>
                                    </div>

                                    {{-- System Title --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="system_title" class="form-label">System Title</label>
                                        <input type="text" name="system_title" id="system_title"
                                            value="{{ old('system_title', $settings->system_title) }}"
                                            class="form-control @error('system_title') is-invalid @enderror"
                                            placeholder="Enter system title">
                                        @error('system_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- System Short Title --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="system_short_title" class="form-label">System Short Title</label>
                                        <input type="text" name="system_short_title" id="system_short_title"
                                            value="{{ old('system_short_title', $settings->system_short_title) }}"
                                            class="form-control @error('system_short_title') is-invalid @enderror"
                                            placeholder="Enter short title">
                                        @error('system_short_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Company Name --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text" name="company_name" id="company_name"
                                            value="{{ old('company_name', $settings->company_name) }}"
                                            class="form-control @error('company_name') is-invalid @enderror"
                                            placeholder="Enter company name">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Tagline --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="tagline" class="form-label">Tagline</label>
                                        <input type="text" name="tagline" id="tagline"
                                            value="{{ old('tagline', $settings->tagline) }}"
                                            class="form-control @error('tagline') is-invalid @enderror"
                                            placeholder="Enter tagline">
                                        @error('tagline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Company Address --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="company_address" class="form-label">Company Address</label>
                                        <input type="text" name="company_address" id="company_address"
                                            value="{{ old('company_address', $settings->company_address) }}"
                                            class="form-control @error('company_address') is-invalid @enderror"
                                            placeholder="Enter company address">
                                        @error('company_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Phone --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" name="phone" id="phone"
                                            value="{{ old('phone', $settings->phone) }}"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            placeholder="Enter phone number">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email"
                                            value="{{ old('email', $settings->email) }}"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Enter email address">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Timezone --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="timezone" class="form-label">Timezone</label>
                                        <input type="text" name="timezone" id="timezone"
                                            value="{{ old('timezone', $settings->timezone) }}"
                                            class="form-control @error('timezone') is-invalid @enderror"
                                            placeholder="e.g., Asia/Dhaka">
                                        @error('timezone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Language --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="language" class="form-label">Language</label>
                                        <input type="text" name="language" id="language"
                                            value="{{ old('language', $settings->language) }}"
                                            class="form-control @error('language') is-invalid @enderror"
                                            placeholder="e.g., en, bn">
                                        @error('language')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Copyright Text --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="copyright_text" class="form-label">Copyright Text</label>
                                        <textarea name="copyright_text" id="copyright_text"
                                            class="form-control @error('copyright_text') is-invalid @enderror" rows="3"
                                            placeholder="Enter copyright text">{{ old('copyright_text', $settings->copyright_text) }}</textarea>
                                        @error('copyright_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Submit Button --}}
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Update Settings
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function previewImage(input, imgContainer) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let existingImg = imgContainer.querySelector('.mb-2 img');
                        if (existingImg) {
                            existingImg.src = e.target.result;
                        } else {
                            const div = document.createElement('div');
                            div.className = 'mb-2';
                            const img = document.createElement('img');
                            img.className = 'img-thumbnail';
                            img.style.height = input.id === 'system_favicon' ? '40px' : '60px';
                            img.src = e.target.result;
                            img.alt = 'Preview';
                            div.appendChild(img);
                            input.parentElement.insertBefore(div, input);
                        }
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            const logoInput = document.getElementById('system_logo');
            if (logoInput) {
                logoInput.addEventListener('change', function() {
                    previewImage(this, this.parentElement);
                });
            }

            const faviconInput = document.getElementById('system_favicon');
            if (faviconInput) {
                faviconInput.addEventListener('change', function() {
                    previewImage(this, this.parentElement);
                });
            }
        });
    </script>
@endpush
