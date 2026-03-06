@extends('backend.master')

@section('body')
<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Property Details</h4>
    <a href="{{ route('admin.booking.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">


        <div class="row mb-3"><div class="col-md-3"><strong>Title:</strong></div><div class="col-md-9">{{ $booking->property->title }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Location:</strong></div><div class="col-md-9">{{ $booking->property->location }}</div></div>
        
        <div class="row mb-3"><div class="col-md-3"><strong>Price:</strong></div><div class="col-md-9">${{ number_format($booking->total_price,2) }}</div></div>
        
        <div class="row mb-3"><div class="col-md-3"><strong>Adults:</strong></div><div class="col-md-9">{{ $booking->adults }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Children:</strong></div><div class="col-md-9">{{ $booking->children }}</div></div>
        
        <div class="row mb-3"><div class="col-md-3"><strong>Checkin:</strong></div><div class="col-md-9">{{ $booking->start_date }}</div></div>
        <div class="row mb-3"><div class="col-md-3"><strong>Checkout:</strong></div><div class="col-md-9">{{ $booking->end_date }}</div></div>
        
        <div class="row mb-3"><div class="col-md-3"><strong>Nights:</strong></div><div class="col-md-9">{{ $booking->nights }}</div></div>
        

        <div class="row mb-3"><div class="col-md-3"><strong>Status:</strong></div><div class="col-md-9">{{ $booking->property->status}}</div>
    </div>

    </div>
</div>
@endsection
