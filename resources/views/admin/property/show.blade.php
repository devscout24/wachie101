@extends('backend.master')

@section('body')
<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Property Details</h4>
    <a href="{{ route('admin.property.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">

        {{-- Multiple Images --}}
        <div class="row mb-3">
            <div class="col-md-3"><strong>Multiple Images:</strong></div>
            <div class="col-md-9">
                @if($property->images && $property->images->count() > 0)
                    @foreach($property->images as $img)
                        <img src="{{ asset($img->image) }}" style="width:120px; height:80px; object-fit:cover; margin-right:5px; margin-bottom:5px;">
                    @endforeach
                @else
                    No Images
                @endif
            </div>
        </div>

        <div class="row mb-3"><div class="col-md-3"><strong>Title:</strong></div><div class="col-md-9">{{ $property->title }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Location:</strong></div><div class="col-md-9">{{ $property->location }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Price:</strong></div><div class="col-md-9">${{ number_format($property->price,2) }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Beds:</strong></div><div class="col-md-9">{{ $property->bedrooms }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Baths:</strong></div><div class="col-md-9">{{ $property->bathrooms }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Guests:</strong></div><div class="col-md-9">{{ $property->max_guests }}</div></div>

        {{-- Amenities --}}
        <div class="row mb-3">
            <div class="col-md-3"><strong>Amenities:</strong></div>
            <div class="col-md-9">{{ $property->amenities->pluck('name')->implode(', ') }}</div>
        </div>

        {{-- Description --}}
        <div class="row mb-3">
            <div class="col-md-3"><strong>Description:</strong></div>
            <div class="col-md-9">{!! $property->description !!}</div>
        </div>

        <div class="row mb-3"><div class="col-md-3"><strong>Status:</strong></div><div class="col-md-9">{{ $property->status == 1 ? 'Active' : 'Inactive' }}</div></div>

    </div>
</div>
@endsection
