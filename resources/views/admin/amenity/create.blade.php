@extends('backend.master')

@section('body')
<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Create New Amenity</h4>
    <a href="{{ route('admin.amenity.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.amenity.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">

            

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ref Name</label>
                        <input type="text" name="ref_name" id="ref_name" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" id="image" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection