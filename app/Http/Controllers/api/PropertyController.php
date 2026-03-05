<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Review;
use App\Traits\apiresponse;
use DateTime;
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


        $perPage = 10;
        $currentPage = $request->get('page', 1);

        $paginator = Property::with(['images'])->orderBy('created_at','desc')->paginate($perPage);

        $properties =  $paginator->through(function ($item) {
            $rating = Review::where('property_id', $item->id)->avg('rating');
            return [
                'id'         => $item->id,
                'name'        => $item->title ?? null,
                'address'     => $item->location ?? null,
                'price'       => $item->price ?? null,
                'maxPeople'   => $item->max_guests ?? null,
                'cover_image' => $item->images()?->first()?->image ?? null,
                'rating'     => $rating,
            ];
        });

        $total = Property::count();

        return response()->json([
            'status'  => true,
            'success' => true,
            'data'    => $properties->items(),
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
        
        
        $property = Property::find($id);

        if(!$property) {
            return response()->json([
                'success' => false,
                'message' => 'property not found'
            ]);
        }


            // Convert response to collection and map it
        $property = Property::with('images')->where('id', $property->id)->first();
        

        
        return response()->json([
            'success' => true,
            'status' => true,
            'data'   => [
                'id'            => $property->id,
                'name'          => $property->title ?? null,
                'address'       => $property->location ?? null,
                'latitude'      => isset($property->latitude) ? number_format((float)$property->latitude, 8, '.', '') : null,
                'longitude'     => isset($property->longitude) ? number_format((float)$property->longitude, 8, '.', '') : null,
                'booking_fee_percentage' => $property->booking_fee ?? null,
                'price'         => $property->price ?? null,
                'maxPeople'         => $property->max_guests ?? null,
                'cleaning_fee'    => $property->cleaning_fee ?? 0,
                'amenities'     => $property->amenities->pluck('name')->all(),
                'property_info'         => $property->description ?? null,  // Property description 1
                'local_area'         => $property->local_area ?? null,  // Property description 2
                'images'        => $property->images->pluck('image')->all(),
            ],
        ]);

        
    }

    public function availableDates(Request $request, $id)
    {
        $property = Property::find($id);
        
        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ]);
        }

        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        try {
            // Get all bookings that overlap with the requested date range
            $bookings = Booking::where('property_id', $property->id)
                ->where(function ($query) use ($start_date, $end_date) {
                    // Booking starts within the range
                    $query->whereBetween('start_date', [$start_date, $end_date])
                        // Booking ends within the range
                        ->orWhereBetween('end_date', [$start_date, $end_date])
                        // Booking completely encompasses the range
                        ->orWhere(function ($q) use ($start_date, $end_date) {
                            $q->where('start_date', '<=', $start_date)
                            ->where('end_date', '>=', $end_date);
                        });
                })
                ->get();

            $unavailableDates = [];

            foreach ($bookings as $booking) {
                // Generate ALL dates between start_date and end_date (inclusive)
                $startDate = new DateTime($booking->start_date);
                $endDate = new DateTime($booking->end_date);
                
                // Loop through each day in the booking period
                while ($startDate <= $endDate) {
                    $unavailableDates[] = $startDate->format('Y-m-d');
                    $startDate->modify('+1 day');
                }
            }

            // Remove duplicates and reindex array
            $unavailableDates = array_values(array_unique($unavailableDates));

            return response()->json([
                'success' => true,
                'blocked_dates' => $unavailableDates
            ]);

        } catch (Exception $e) {
            Log::error('Available Dates Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    } 
}
