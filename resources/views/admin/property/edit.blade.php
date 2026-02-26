@extends('backend.master')

@section('body')
<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Edit Property</h4>
    <a href="{{ route('admin.property.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.property.update', $property->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- ✅ Multiple Images --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Multiple Images</label>
                    <input type="file" name="multiple_image[]" class="form-control" multiple>

                    {{-- Show Existing Images --}}
                    @if($property->images->count() > 0)
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @foreach ($property->images as $img)
                                <div class="position-relative">
                                    <img src="{{ asset($img->image) }}" class="img-thumbnail" width="90" height="70" style="object-fit:cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Title --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $property->title }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">propertyId</label>
                    <input type="text" name="property_ref_id" class="form-control" value="{{ $property->property_ref_id }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">roomId</label>
                    <input type="text" name="room_ref_id" class="form-control" value="{{ $property->room_ref_id }}" required>
                </div>

                {{-- Location --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ $property->location }}" required>
                </div>

                {{-- Price --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" name="price" step="0.01" class="form-control" value="{{ $property->price }}" required>
                </div>

                {{-- Bedrooms --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label">Bedrooms</label>
                    <input type="number" name="bedrooms" class="form-control" value="{{ $property->bedrooms }}" required>
                </div>

                {{-- Bathrooms --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label">Bathrooms</label>
                    <input type="number" name="bathrooms" class="form-control" value="{{ $property->bathrooms }}" required>
                </div>

                {{-- Max Guests --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label">Max Guests</label>
                    <input type="number" name="max_guests" class="form-control" value="{{ $property->max_guests }}" required>
                </div>

                {{-- ✅ Amenities --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Amenities</label>
                    <select name="amenity_id[]" class="form-select" multiple>
                        @foreach ($amenities as $amenity)
                            <option value="{{ $amenity->id }}"
                                {{ $property->amenities->contains('id', $amenity->id) ? 'selected' : '' }}>
                                {{ $amenity->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Description --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5">{{ $property->description }}</textarea>
                </div>

            </div>

            <button type="submit" class="btn btn-success mt-3">Update Property</button>
        </form>
    </div>
</div>
@endsection
