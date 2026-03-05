@extends('backend.master')

@section('body')
    <div class="d-flex justify-content-between mt-4 mb-3">
        <h4>Booking List</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="propertyTable" class="table table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Adults</th>
                        <th>Children</th>
                        <th>Total Price</th>
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
                        data: 'start_date'
                    }, {
                        data: 'end_date'
                    }, {
                        data: 'adults'
                    }, {
                        data: 'children'
                    },
                    {
                        data: 'total_price'
                    }, {
                        data: 'payment_status'
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
