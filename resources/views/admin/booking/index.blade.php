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
            var table = $('#propertyTable').DataTable({
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
                        
                        // Re-enable the select
                        $select.prop('disabled', false);
                        
                        $select.addClass('border-success');
                        setTimeout(() => $select.removeClass('border-success'), 2000);
                    } else {
                        alert('Failed to update status.');
                        $select.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                    $select.prop('disabled', false);
                }
            });
        });

    </script>
@endpush
