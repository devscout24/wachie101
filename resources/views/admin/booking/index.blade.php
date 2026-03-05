@extends('backend.master')

@section('body')
    <div class="d-flex justify-content-between mt-4 mb-3">
        <h4>Booking List</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="bookingTable" class="table table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
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
            var table = $('#bookingTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.booking.index') }}",
                scrollX: true,
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    }, 
                    {
                        data: 'start_date'
                    }, 
                    {
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

            $('#bookingTable').on('change', '.status-change', function() {
                var $select = $(this);
                var bookingId = $select.data('id');
                var newStatus = $select.val();

                $select.prop('disabled', true);

                $.ajax({
                    url: '{{ route("admin.booking.updateStatus") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: bookingId,
                        payment_status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Booking status updated successfully!',
                                timer: 2000,
                                timerProgressBar: true,
                                didClose: function() {
                                    // Re-enable the select after alert closes
                                    $select.prop('disabled', false);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: 'Failed to update status.'
                            });
                            $select.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error updating status. Please try again.'
                        });
                        $select.prop('disabled', false);
                    }
                });
            });
        });

        

    </script>
@endpush
