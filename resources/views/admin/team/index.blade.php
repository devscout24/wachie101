@extends('backend.master')

@section('body')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h4 class="mb-0">Team Members</h4>
        <a href="{{ route('admin.team.create') }}" class="btn btn-primary">Add New</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="teamTable" class="table table-hover table-bordered w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Designation</th>
                        <th>Bio</th>
                        <th>Status</th>
                        <th width="150px">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            let table = $('#teamTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.team.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'designation'
                    },
                    {
                        data: 'bio',
                        orderable: false
                    },
                    {
                        data: 'is_active',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // DELETE
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');

                if (!confirm('Are you sure you want to delete this team member?')) return;

                $.ajax({
                    url: '/admin/teams/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            table.ajax.reload(null, false);
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Delete failed! Check console.');
                    }

                });
            });

        });
    </script>
@endpush
