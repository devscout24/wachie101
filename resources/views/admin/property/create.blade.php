@extends('backend.master')

@section('body')
<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Create New Property</h4>
    <a href="{{ route('admin.property.index') }}" class="btn btn-secondary">Property List</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.property.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Multiple Images</label>
                    <input type="file" name="multiple_image[]" class="form-control" multiple>
                    @error('multiple_image.*')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">propertyId</label>
                    <input type="text" name="property_ref_id" class="form-control" value="{{ old('property_ref_id') }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">roomId</label>
                    <input type="text" name="room_ref_id" class="form-control" value="{{ old('room_ref_id') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">latitude</label>
                    <input type="number" name="latitude" class="form-control" value="{{ old('latitude') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">longitude</label>
                    <input type="number" name="longitude" class="form-control" value="{{ old('longitude') }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">city</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">state</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">postcode</label>
                    <input type="text" name="postcode" class="form-control" value="{{ old('postcode') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">mobile</label>
                    <input type="phone" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price') }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Cleaning Fee ($)</label>
                    <input type="number" name="cleaning_fee" step="0.01" class="form-control" value="{{ old('cleaning_fee') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Bedrooms</label>
                    <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms',0) }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Bathrooms</label>
                    <input type="number" name="bathrooms" class="form-control" value="{{ old('bathrooms',0) }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Max Guests</label>
                    <input type="number" name="max_guests" class="form-control" value="{{ old('max_guests',0) }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">Max Children</label>
                    <input type="number" name="max_children" class="form-control" value="{{ old('max_children',0) }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Amenities</label>
                    <select name="amenity_id[]" class="form-select" multiple>
                        @foreach($amenities as $amenity)
                            <option value="{{ $amenity->id }}">{{ $amenity->ref_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control summernote" rows="5">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

            </div>

            <button type="submit" class="btn btn-success mt-3">Create Property</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('.summernote').summernote({height:200});
    $('select[multiple]').select2({placeholder:"Select amenities",width:'100%'});
});
</script>
@endpush
