<?php

namespace App\Console\Commands;

use App\Models\Amenity;
use App\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckForNewPropertyCommand extends Command
{
    protected $signature = 'app:check-for-new-property-command';

    protected $description = 'Fetch properties from Beds24 API and create or update local records';

    public function handle()
    {
        $token = config('services.beds24.token');

        if (!$token) {
            $this->error('Beds24 token not found in config.');
            return Command::FAILURE;
        }

        $this->info('Fetching properties from Beds24...');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
        ])->withQueryParameters([
            'includeLanguages'    => 'all',
            'includeTexts'        => 'all',
            'includePictures'     => true,
            'includeOffers'       => true,
            'includePriceRules'   => true,
            'includeUpsellItems'  => true,
            'includeAllRooms'     => true,
            'includeUnitDetails'  => true,
        ])->get('https://beds24.com/api/v2/properties');

        if (!$response->successful()) {
            $this->error('Failed to fetch properties from Beds24 API. Status: ' . $response->status());
            return Command::FAILURE;
        }

        $responseData = $response->json();

        if (empty($responseData['data'])) {
            $this->warn('No properties found in API response.');
            return Command::SUCCESS;
        }

        // Load all amenities keyed by ref_name for quick lookup
        $amenities = Amenity::all()->keyBy('ref_name');

        $properties = collect($responseData['data']);

        $created = 0;
        $updated = 0;

        foreach ($properties as $item) {
            // Get property level texts (English)
            $propertyTexts = $item['texts'][0] ?? null;

            // Get the first room type for price/guest info
            $firstRoom = $item['roomTypes'][0] ?? null;

            // Get booking fee percentage from booking rules
            $bookingFee = $item['bookingRules']['vatRatePercentage'] ?? 0;

            // Map feature codes to local amenity IDs via ref_name
            $featureCodes = collect($item['featureCodes'] ?? [])
                ->map(fn($code) => is_array($code) ? $code[0] : $code)
                ->filter()
                ->map(fn($code) => $amenities->get($code)?->id)
                ->filter()
                ->values()
                ->toArray();

            $data = [
                'user_id'         => 1,
                'property_ref_id' => $item['id'] ?? null,
                'room_ref_id'     => $firstRoom['id'] ?? null,
                'title'           => $item['name'] ?? 'Untitled Property',
                'location'        => $item['address'] ?? '',
                'latitude'        => isset($item['latitude']) ? number_format((float)$item['latitude'], 8, '.', '') : 0,
                'longitude'       => isset($item['longitude']) ? number_format((float)$item['longitude'], 8, '.', '') : 0,
                'price'           => $firstRoom['minPrice'] ?? 0,
                'cleaning_fee'    => $firstRoom['cleaningFee'] ?? 0,
                'booking_fee'     => $bookingFee,
                'max_guests'      => $firstRoom['maxPeople'] ?? 0,
                'max_children'    => $firstRoom['maxChildren'] ?? 0,
                'description'     => $propertyTexts['propertyDescription1'] ?? null,
                'status'          => 1,
            ];

            $property = Property::where('property_ref_id', $item['id'])->first();

            if ($property) {
                $property->update($data);
                $updated++;
                $this->line("  Updated: {$data['title']} (ref_id: {$item['id']})");
            } else {
                $property = Property::create($data);
                $created++;
                $this->line("  Created: {$data['title']} (ref_id: {$item['id']})");
            }

            // Sync amenities via pivot relationship
            if (method_exists($property, 'amenities')) {
                $property->amenities()->sync($featureCodes);
            }
        }

        $this->info("Done! Created: {$created}, Updated: {$updated}");

        return Command::SUCCESS;
    }
}