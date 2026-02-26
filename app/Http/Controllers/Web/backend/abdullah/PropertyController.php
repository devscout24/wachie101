<?php

namespace App\Http\Controllers\Web\backend\abdullah;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyMultipleImage;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PropertyController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Property::with(['images', 'amenities'])->select('properties.*');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('multiple_image', function ($row) {
                    if ($row->images->count() > 0) {
                        $img = asset($row->images->first()->image);
                        return '<img src="' . $img . '" style="width:80px;height:40px;object-fit:cover;">';
                    }
                    return 'No Image';
                })
                ->addColumn('amenity_id', function ($row) {
                    return $row->amenities->pluck('name')->implode(', ');
                })
                ->addColumn('price', function ($row) {
                    return '$' . number_format($row->price, 2);
                })
                ->addColumn('cleaning_fee', function ($row) {
                    return '$' . number_format($row->cleaning_fee, 2);
                })
                ->addColumn('description', function ($row) {
                    return Str::limit(strip_tags($row->description), 30);
                })
                ->addColumn('status', function ($row) {
                    return $row->status ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('admin.property.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <a href="' . route('admin.property.show', $row->id) . '" class="btn btn-sm btn-info">Show</a>
                        <button data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-delete">Delete</button>
                    ';
                })
                ->rawColumns(['multiple_image', 'action'])
                ->make(true);
        }

        return view('admin.property.index');
    }



    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'location'         => 'required|string|max:255',
            'price'            => 'required|numeric',
            'amenity_id'       => 'nullable|array',
            'amenity_id.*'     => 'exists:amenities,id',
            'cleaning_fee'     => 'nullable|numeric',
            'multiple_image'   => 'nullable|array',
            'multiple_image.*' => 'file|mimes:jpg,jpeg,png,gif,webp,svg,avif|max:5120',
        ]);

        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'error' => 'Beds24 token not found'
            ]);
        }

        $data = $request->except(['multiple_image', 'amenity_id']);
        $data['user_id'] = Auth::id() ?? 1;

        $property = Property::create($data);

        // $response = Http::withHeaders([
        //     'accept' => 'application/json',
        //     'token' => $token,
        //     'Content-Type' => 'application/json',
        // ])->post('https://beds24.com/api/v2/properties', [
        //     [
        //         "name" => $request->title,
        //         "propertyType" => "apartment",
        //         "currency" => "AUD",
        //         "address" => $request->location,
        //         "city" => $request->city,
        //         "state" => $request->state,
        //         "country" => "Australia",
        //         "postcode" => $request->postcode,
        //         "mobile" => $request->mobile,
        //     ]
        // ]);

        // $dataArray = $response->json()[0];
        // $property->property_ref_id = $dataArray["new"]["id"];
        // $property->save();

        // $response = Http::withHeaders([
        //     'accept' => 'application/json',
        //     'token' => $token,
        //     'Content-Type' => 'application/json',
        // ])->post('https://beds24.com/api/v2/properties', [
        //     [
        //         "id" => $property->property_ref_id,
        //         "roomTypes" => [
        //             [
        //                 "name" => "Standard room",
        //                 "qty" => $request->max_guests,
        //                 "maxAdult" => $request->max_guests - $request->max_children,
        //                 "maxChildren" => $request->max_children
        //             ]
        //         ]
        //     ]
        // ]);

        // $dataArray = $response->json()[0];
        // $property->room_ref_id = $dataArray["new"]['roomTypes']["id"];
        // $property->save();

        // Save multiple images
        if ($request->hasFile('multiple_image')) {
            foreach ($request->file('multiple_image') as $file) {
                $path = 'uploads/properties/';
                $name = uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path($path), $name);

                PropertyMultipleImage::create([
                    'property_id' => $property->id,
                    'image'       => $path . $name,
                ]);

                unset($path, $name);
            }
        }

        // Sync amenities
        if ($request->amenity_id) {
            $property->amenities()->sync($request->amenity_id);
        }

        return redirect()->route('admin.property.index')->with('success', 'Property Created');
    }


    // CREATE
    public function create()
    {
        $amenities = Amenity::where('status', 1)->orderBy('name')->get();
        return view('admin.property.create', compact('amenities'));
    }


    public function show($id)
    {
        // Eager load amenities and images
        $property = Property::with(['amenities', 'images'])->findOrFail($id);

        return view('admin.property.show', compact('property'));
    }



    public function edit($id)
    {
        $property  = Property::with(['images', 'amenities'])->findOrFail($id);
        $amenities = Amenity::where('status', 1)->orderBy('name')->get();

        return view('admin.property.edit', compact('property', 'amenities'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $request->validate([
            'title'              => 'required|string|max:255',
            'location'           => 'required|string|max:255',
            'price'              => 'required|numeric',
            'cleaning_fee'       => 'nullable|numeric',
            'multiple_image.*'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            'amenity_id'         => 'nullable|array',
            'amenity_id.*'       => 'exists:amenities,id',
        ]);

        // Update normal fields
        $data = $request->except(['multiple_image', 'amenity_id']);
        
        $property->update($data);


        // ✅ Upload & Save new images
        if ($request->hasFile('multiple_image')) {
            foreach ($request->file('multiple_image') as $file) {

                $filename = 'uploads/properties/' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/properties'), basename($filename));

                PropertyMultipleImage::create([
                    'property_id' => $property->id,
                    'image'       => $filename
                ]);
            }
        }

        // ✅ Sync amenities
        $property->amenities()->sync($request->amenity_id ?? []);

        return redirect()->route('admin.property.index')->with('success', 'Property updated successfully');
    }



    public function destroy($id)
    {
        $property = Property::with('images')->findOrFail($id);

        // Delete images from public folder
        if ($property->images->count() > 0) {
            foreach ($property->images as $img) {
                if (file_exists(public_path($img->image))) {
                    @unlink(public_path($img->image));
                }
            }
        }

        // Delete related multiple images records
        $property->images()->delete();

        // Detach amenities
        $property->amenities()->detach();

        // Delete the property itself
        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }



    //     public function toggleStatus($id)
    // {
    //     $news = News::find($id);
    //     if (!$news) {
    //         return response()->json(['success' => false]);
    //     }

    //     $news->status = $news->status == 1 ? 0 : 1;
    //     $news->save();

    //     return response()->json([
    //         'success' => true,
    //         'status'  => $news->status
    //     ]);
    // }
    
}
