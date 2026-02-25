@extends('backend.master')

@section('body')

<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h4 class="mb-0">Edit Team Member</h4>
    <a href="{{ route('admin.team.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.team.update', $team->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- Name --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $team->name) }}"
                               required>
                    </div>
                </div>

                {{-- Designation --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Designation</label>
                        <input type="text"
                               name="designation"
                               class="form-control"
                               value="{{ old('designation', $team->designation) }}"
                               required>
                    </div>
                </div>

                {{-- Current Image --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Current Image</label><br>
                        @if($team->image)
                            <img src="{{ asset($team->image) }}" width="80" height="80" style="object-fit:cover;border-radius:50%;">
                        @else
                            <span class="text-muted">No image uploaded</span>
                        @endif
                    </div>
                </div>

                {{-- Change Image --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Change Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                {{-- Bio --}}
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea name="bio"
                                  class="form-control summernote"
                                  rows="4">{{ old('bio', $team->bio) }}</textarea>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $team->is_active) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $team->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

@endsection
