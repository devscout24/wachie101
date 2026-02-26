<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Property;
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

    public function index()
    {
        // $property = Property::with(['amenities:id,name', 'images'])->find($id);

        // if (!$property) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Property not found'
        //     ], 404);
        // }

        // // ✅ MULTIPLE IMAGE FROM RELATION
        // $property->multiple_image = $property->images->map(function ($img) {
        //     return url($img->image);
        // })->values();

        // // ✅ STRIP TAG DESCRIPTION
        // $property->description = strip_tags($property->description);

        // // optional: remove images relation from response
        // unset($property->images);

        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'error' => 'Beds24 token not found'
            ]);
        }

        $paginator = Property::latest()->paginate(10);

        $refIds = $paginator->pluck('property_ref_id')->toArray();

        
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
        ])->withQueryParameters([
            'id' => $refIds, 
            'includeLanguages'    => 'all',
            'includeTexts'        => 'all',
            'includePictures'     => true,
            'includeOffers'       => true,
            'includePriceRules'   => true,
            'includeUpsellItems'  => true,
            'includeAllRooms'     => true,
            'includeUnitDetails'  => true,
        ])->get('https://beds24.com/api/v2/properties');

            // Convert response to collection and map it
        $properties = collect($response->json()['data'])->map(function ($item) {
            // Get property level texts (English)
            
            // Get the first room type for price
            $firstRoom = isset($item['roomTypes'][0]) ? $item['roomTypes'][0] : null;
                        
            return [
                'id'            => $item['id'] ?? null,
                'name'          => $item['name'] ?? null,
                'address'       => $item['address'] ?? null,
                'price'         => $firstRoom['minPrice'] ?? null,
                'maxPeople'         => $firstRoom['maxPeople'] ?? null,
            ];
        });

        return response()->json([
            'status' => $response->status(),
            'success' => true,
            'data'   => $properties,
             'message' => 'Property retrieved successfully'
        ]);
    }

    public function getone($id)
    {
        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'error' => 'Beds24 token not found'
            ]);
        }
        
        $property = Property::find($id);

        if(!$property) {
            return response()->json([
                'success' => false,
                'message' => 'property not found'
            ]);
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
        ])->withQueryParameters([
            'id'                  => $property->property_ref_id,
            'includeLanguages'    => 'all',
            'includeTexts'        => 'all',
            'includePictures'     => true,
            'includeOffers'       => true,
            'includePriceRules'   => true,
            'includeUpsellItems'  => true,
            'includeAllRooms'     => true,
            'includeUnitDetails'  => true,
            'roomId'              => $property->room_ref_id,

        ])->get('https://beds24.com/api/v2/properties');

            // Convert response to collection and map it
        $properties = collect($response->json()['data'])->map(function ($item) {
            // Get property level texts (English)
            $propertyTexts = isset($item['texts'][0]) ? $item['texts'][0] : null;
            
            // Get the first room type for price
            $firstRoom = isset($item['roomTypes'][0]) ? $item['roomTypes'][0] : null;
            
            // Get property level upsell items
            $propertyUpsells = $item['upsellItems'] ?? [];
            $obligatoryUpsells = collect($propertyUpsells)
                ->filter(function($upsell) {
                    return ($upsell['type'] ?? '') === 'obligatory';
                })
                ->map(function($upsell) use ($propertyTexts) {
                    $index = $upsell['index'] ?? 0;
                    return [
                        'name' => $propertyTexts['upsellItemName' . $index] ?? null,
                        'amount' => $upsell['amount'] ?? null,
                        'per' => $upsell['per'] ?? null,
                        'period' => $upsell['period'] ?? null,
                        // 'vat' => $upsell['vat'] ?? null
                    ];
                })
                ->values()
                ->toArray();
                
            return [
                'id'            => $item['id'] ?? null,
                'name'          => $item['name'] ?? null,
                'address'       => $item['address'] ?? null,
                'latitude'      => isset($item['latitude']) ? number_format((float)$item['latitude'], 8, '.', '') : null,
                'longitude'     => isset($item['longitude']) ? number_format((float)$item['longitude'], 8, '.', '') : null,
                'booking_fee_percentage' => $item['bookingRules']['vatRatePercentage'] ?? null,
                'price'         => $firstRoom['minPrice'] ?? null,
                'maxPeople'         => $firstRoom['maxPeople'] ?? null,
                'obligatory_upsells' => $obligatoryUpsells,
                'amenities'     => $item['featureCodes'] ?? null,
                'property_info'         => $propertyTexts['propertyDescription1'] ?? null,  // Property description 1
                'local_area'         => $propertyTexts['propertyDescription2'] ?? null,  // Property description 2
            ];
        });

        return response()->json([
            'success' => true,
            'status' => $response->status(),
            'data'   => $properties,
        ]);

        
    }

    public function availableDates(Request $request, $id){

        $propertyId = 313566;

        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        $token = Config::get('services.beds24.token') ?? env('BEDS24_API_TOKEN');
        if (!$token) {
            throw new Exception('Beds24 API Token is missing.');
        }

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'token'  => $token,
            ])->get('https://beds24.com/api/v2/inventory/rooms/availability', [
                'propertyId' => $propertyId,
                'startDate'  => trim($start_date), 
                'endDate'    => trim($end_date),
            ]);

            if ($response->failed()) {
                Log::error('Beds24 API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);
                
                return response()->json([
                    'error' => 'Failed to fetch availability',
                    'details' => $response->json() ?? $response->body()
                ], $response->status());
            }

            $dataArray = $response->json()['data'][0];
            
            $availability = $dataArray['availability'] ?? null;

            
            $unavailableDates = array_keys(
                array_filter($availability, fn($isAvailable) => $isAvailable === false)
            );

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
