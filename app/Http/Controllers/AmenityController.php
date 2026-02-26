<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amenity;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class AmenityController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Amenity::query()->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('name', fn($row) => e($row->name))
                ->addColumn('ref_name', fn($row) => e($row->ref_name))

                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset($row->image) . '" class="" width="100" height="40">';
                    }
                    return 'No Image';
                })

                ->addColumn('status', function ($row) {
                    return $row->status == 1 ? 'Active' : 'Inactive';
                })

                ->addColumn('action', function ($row) {
                    return '
                    <a href="' . route('admin.amenity.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                    <form action="' . route('admin.amenity.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>
                ';
                })

                ->rawColumns(['image', 'action']) // ✅ Add 'action' here
                ->make(true);
        }

        return view('admin.amenity.index');
    }


    public function create()
    {
        return view('admin.amenity.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $amenity = new Amenity();
        
        $amenity->name = $request->name;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/amenities'), $imageName);
            $amenity->image = 'uploads/amenities/' . $imageName;
        }
        $amenity->status = $request->status;
        $amenity->save();

        return redirect()->route('admin.amenity.index')->with('success', 'Amenity created successfully.');
    }

    public function edit($id)
    {
        $amenity = Amenity::findOrFail($id);
        return view('admin.amenity.edit', compact('amenity'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $amenity = Amenity::findOrFail($id);
        $amenity->name = $request->name;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/amenities'), $imageName);
            $amenity->image = 'uploads/amenities/' . $imageName;
        }

        $amenity->save();

        return redirect()->route('admin.amenity.index')->with('success', 'Amenity updated successfully.');
    }

    public function destroy($id)
    {
        $amenity = Amenity::findOrFail($id);
        $amenity->delete();

        return redirect()->route('admin.amenity.index')->with('success', 'Amenity deleted successfully.');
    }
}
