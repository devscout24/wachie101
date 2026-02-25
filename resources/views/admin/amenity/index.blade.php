@extends('backend.master')


@section('body')
<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Amenity List</h4>
    <a href="{{ route('admin.amenity.create') }}" class="btn btn-primary">+ Add Amenity</a>
</div>

<div class="card">
    <div class="card-body">
        <table id="amenityTable" class="table table-bordered" style="width:100%">
            <thead>
            <tr>
                <th>#</th>
                {{-- <th>Property ID</th> --}}
                <th>Name</th>
                <th>Image</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    var table = $('#amenityTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.amenity.index') }}",
        pagingType: "full_numbers",
        responsive: true,
        autoWidth: false,
        columns: [
            { data: 'DT_RowIndex', searchable: false, orderable: false },
            // { data: 'property_id' },
            { data: 'name' },
            { data: 'image', searchable: false, orderable: false },
            { data: 'status' },
            { data: 'action', searchable: false, orderable: false }
        ]
    });
});
</script>
@endpush    

