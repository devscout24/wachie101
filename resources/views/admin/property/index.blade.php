@extends('backend.master')

@section('body')
    <div class="d-flex justify-content-between mt-4 mb-3">
        <h4>Property List</h4>
        <a href="{{ route('admin.property.create') }}" class="btn btn-primary">+ Add Property</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="propertyTable" class="table table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Multiple Image</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Cleaning Fee</th>
                        <th>Beds</th>
                        <th>Baths</th>
                        <th>Guests</th>
                        <th>Amenities</th>
                        <th>Description</th>
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
            var table = $('#propertyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.property.index') }}",
                scrollX: true,
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'multiple_image',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'title'
                    }, {
                        data: 'location'
                    }, {
                        data: 'price'
                    }, {
                        data: 'cleaning_fee'
                    },
                    {
                        data: 'bedrooms'
                    }, {
                        data: 'bathrooms'
                    }, {
                        data: 'max_guests'
                    },
                    {
                        data: 'amenity_id'
                    }, {
                        data: 'description'
                    }, {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        searchable: false,
                        orderable: false
                    }
                ]
            });

            $(document).on('click', '.btn-delete', function() {
                if (!confirm('Delete this property?')) return;

                let id = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/properties/delete') }}/" +
                        id, // match your route: /delete/{id}
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        $('#propertyTable').DataTable().ajax.reload();
                        alert(res.message || 'Deleted');
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message || 'Something went wrong');
                    }
                });
            });

        });
    </script>
@endpush
