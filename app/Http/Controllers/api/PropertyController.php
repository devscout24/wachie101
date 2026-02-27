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

    public function index(Request $request)
    {

        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'error' => 'Beds24 token not found'
            ]);
        }

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
        $perPage = 10;
        $currentPage = $request->get('page', 1);

        // Get ALL property ref IDs (not paginated locally)
        $allRefIds = Property::pluck('property_ref_id')->toArray();

        // Chunk ref IDs for current page (10 per page)
        $pagedRefIds = array_slice($allRefIds, ($currentPage - 1) * $perPage, $perPage);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
        ])->withQueryParameters([
            'id'                  => $pagedRefIds,
            'includeLanguages'    => 'all',
            'includeTexts'        => 'all',
            'includePictures'     => true,
            'includeOffers'       => true,
            'includePriceRules'   => true,
            'includeUpsellItems'  => true,
            'includeAllRooms'     => true,
            'includeUnitDetails'  => true,
        ])->get('https://beds24.com/api/v2/properties');

        $properties = collect($response->json()['data'] ?? [])->map(function ($item) {
            $firstRoom = $item['roomTypes'][0] ?? null;

            return [
                'id'         => Property::where('property_ref_id', $item['id'])->value('id'),
                'property_id' => $item['id'] ?? null,
                'name'        => $item['name'] ?? null,
                'address'     => $item['address'] ?? null,
                'price'       => $firstRoom['minPrice'] ?? null,
                'maxPeople'   => $firstRoom['maxPeople'] ?? null,
            ];
        });

        $total = count($allRefIds);

        return response()->json([
            'status'  => $response->status(),
            'success' => true,
            'data'    => $properties,
            'pagination' => [
                'current_page' => $currentPage,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
                'has_more'     => $response->json()['pages']['nextPageExists'] ?? false,
            ],
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
                'id'            => Property::where('property_ref_id', $item['id'])->value('id'),
                'property_id'            => $item['id'] ?? null,
                'name'          => $item['name'] ?? null,
                'address'       => $item['address'] ?? null,
                'latitude'      => isset($item['latitude']) ? number_format((float)$item['latitude'], 8, '.', '') : null,
                'longitude'     => isset($item['longitude']) ? number_format((float)$item['longitude'], 8, '.', '') : null,
                'booking_fee_percentage' => $item['bookingRules']['vatRatePercentage'] ?? null,
                'price'         => $firstRoom['minPrice'] ?? null,
                'maxPeople'         => $firstRoom['maxPeople'] ?? null,
                'obligatory_upsells' => $obligatoryUpsells,
                'cleaning_fee'    => $firstRoom['cleaningFee'] ?? 0,
                'amenities'     => $item['featureCodes'] ?? null,
                'property_info'         => $propertyTexts['propertyDescription1'] ?? null,  // Property description 1
                'local_area'         => $propertyTexts['propertyDescription2'] ?? null,  // Property description 2
            ];
        });

        $property->update([
            'name' => $properties[0]['name'] ?? $property->name,
            'address' => $properties[0]['address'] ?? $property->address,
            'latitude' => $properties[0]['latitude'] ?? $property->latitude,
            'longitude' => $properties[0]['longitude'] ?? $property->longitude,
            'price' => $properties[0]['price'] ?? $property->price,
            'cleaning_fee' => $properties[0]['cleaning_fee'] ?? $property->cleaning_fee,
            'booking_fee' => $properties[0]['booking_fee_percentage'] ?? $property->booking_fee,
        ]);
        
        return response()->json([
            'success' => true,
            'status' => $response->status(),
            'data'   => $properties,
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

        $propertyId = $property->property_ref_id;

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
