@extends('backend.master')

@section('title', 'Users - Index')

@section('body')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Users</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('users.create') }}" class="btn btn-primary">Create New User</a>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- TABLE -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User List</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="users-table">
                    <thead>
                        <tr>
                            <th width="5%">SL No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th width="20%">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')


<script>
    $(document).ready(function() {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('users.data') }}',
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.log('DataTables error:', xhr.responseText);
                    alert('Error loading data. Check console for details.');
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    width: '5%'
                },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { 
                    data: 'created_at', 
                    name: 'created_at',
                    render: function(data) {
                        return data ? new Date(data).toLocaleString() : '';
                    }
                },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    width: '20%'
                }
            ],
            order: [[3, 'desc']], 
            pageLength: 10,
            responsive: true,
            language: {
                processing: "Loading...",
                emptyTable: "No users found",
                zeroRecords: "No matching users found"
            }
        });
    });
</script>
@endpush