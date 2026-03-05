<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Review;
use App\Traits\apiresponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class PropertyController extends Controller
{
    use apiresponse;

    public function index(Request $request)
    {

        // $token = config('services.beds24.token');

        // if (!$token) {
        //     return response()->json([
        //         'error' => 'Beds24 token not found'
        //     ]);
        // }


        $perPage = 10;
        $currentPage = $request->get('page', 1);

        // $allRefIds = Property::pluck('property_ref_id')->toArray();
        // $pagedRefIds = array_slice($allRefIds, ($currentPage - 1) * $perPage, $perPage);
        // $response = Http::withHeaders([
        //     'accept' => 'application/json',
        //     'token'  => $token,
        // ])->withQueryParameters([
        //     'id'                  => $pagedRefIds,
        //     'includeLanguages'    => 'all',
        //     'includeTexts'        => 'all',
        //     'includePictures'     => true,
        //     'includeOffers'       => true,
        //     'includePriceRules'   => true,
        //     'includeUpsellItems'  => true,
        //     'includeAllRooms'     => true,
        //     'includeUnitDetails'  => true,
        // ])->get('https://beds24.com/api/v2/properties');

        $paginator = Property::orderBy('created_at','desc')->paginate($perPage);

        $properties =  $paginator->through(function ($item) {
            $rating = Review::where('property_id', $item->id)->avg('rating');
            return [
                'id'         => $item->id,
                'name'        => $item->name ?? null,
                'address'     => $item->address ?? null,
                'price'       => $firstRoom['minPrice'] ?? null,
                'maxPeople'   => $firstRoom['maxPeople'] ?? null,
                'rating'     => $rating,
            ];
        });

        $total = Property::count();

        return response()->json([
            'status'  => true,
            'success' => true,
            'data'    => $properties,
            'pagination' => [
                'current_page' => $currentPage,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => $paginator->lastPage(),
                'has_more'     => $paginator->hasMorePages(),
            ],
            'message' => 'Property retrieved successfully'
        ]);
    }

    public function getone($id)
    {
        // $token = config('services.beds24.token');

        // if (!$token) {
        //     return response()->json([
        //         'error' => 'Beds24 token not found'
        //     ]);
        // }
        
        $property = Property::find($id);

        if(!$property) {
            return response()->json([
                'success' => false,
                'message' => 'property not found'
            ]);
        }

        // $response = Http::withHeaders([
        //     'accept' => 'application/json',
        //     'token'  => $token,
        // ])->withQueryParameters([
        //     'id'                  => $property->property_ref_id,
        //     'includeLanguages'    => 'all',
        //     'includeTexts'        => 'all',
        //     'includePictures'     => true,
        //     'includeOffers'       => true,
        //     'includePriceRules'   => true,
        //     'includeUpsellItems'  => true,
        //     'includeAllRooms'     => true,
        //     'includeUnitDetails'  => true,
        //     'roomId'              => $property->room_ref_id,

        // ])->get('https://beds24.com/api/v2/properties');

            // Convert response to collection and map it
        $property = Property::where('id', $property->id)->first();
        

        
        return response()->json([
            'success' => true,
            'status' => true,
            'data'   => [
                'id'            => $property->id,
                'name'          => $property->title ?? null,
                'address'       => $property->address ?? null,
                'latitude'      => isset($property->latitude) ? number_format((float)$property->latitude, 8, '.', '') : null,
                'longitude'     => isset($property->longitude) ? number_format((float)$property->longitude, 8, '.', '') : null,
                'booking_fee_percentage' => $property->booking_fee ?? null,
                'price'         => $property->price ?? null,
                'maxPeople'         => $property->max_guests ?? null,
                'cleaning_fee'    => $property->cleaning_fee ?? 0,
                'amenities'     => $property->amenities ?? null,
                'property_info'         => $property->description ?? null,  // Property description 1
                'local_area'         =>  null,  // Property description 2
            ],
        ]);

        
    }

    public function availableDates(Request $request, $id){

        $property = Property::find($id);
        
        if(!$property){
            return response()->json([
                'success'=> false,
                'message' => 'property not found',
            ]);
        }


        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        try {
            $checkIns = Booking::where('property_id', $property->id)
            ->whereBetween('start_date', [$start_date, $end_date])
            ->distinct()
            ->pluck('start_date')->toArray();

            $checkOut = Booking::where('property_id', $property->id)
            ->whereBetween('end_date', [$start_date, $end_date])
            ->distinct()
            ->pluck('end_date')->toArray();

            $unavailableDates = array_unique(array_merge($checkIns, $checkOut));
            return response()->json([
                'success' => true,
                'blocked_dates' => $unavailableDates
            ]);



        } catch (Exception $e) {
            Log::error('Beds24 Request Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }    
}
