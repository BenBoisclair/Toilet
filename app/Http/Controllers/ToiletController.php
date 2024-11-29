<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Toilet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ToiletController extends Controller
{
    /**
     * Display the map with all toilets.
     */
    public function index()
    {
        $toilets = Toilet::with(['facilities', 'discoverer'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get();

        return Inertia::render('Map', ['toilets' => $toilets]);
    }

    /**
     * Show the form for creating a new toilet.
     */
    public function create()
    {
        $facilities = Facility::all();
        return Inertia::render('Toilet/Create', ['facilities' => $facilities]);
    }

    /**
     * Store a newly created toilet in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'facilities' => ['array']
        ]);

        $attributes['discoverer_id'] = $request->user()->id;
        $toilet = Toilet::create($attributes);
        $toilet->facilities()->attach($attributes['facilities'] ?? []);

        return redirect()->route('map')->with('success', 'Toilet added successfully!');
    }

    /**
     * Display the specified toilet.
     */
    public function show(Toilet $toilet)
    {
        $toilet->load([
            'facilities',
            'discoverer',
            'reviews' => function ($query) {
                $query->latest();
            },
            'reviews.reviewer',
            'reviews.gender'
        ])->loadCount('reviews')
          ->loadAvg('reviews', 'rating');

        return Inertia::render('Toilet/Show', ['toilet' => $toilet]);
    }

    /**
     * Return a random toilet.
     */
    public function random()
    {
        $toilet = Toilet::with(['facilities', 'discoverer'])->withCount('reviews')
        ->withAvg('reviews', 'rating')->inRandomOrder()->first();
        sleep(5);
        return response()->json($toilet);
    }

    /**
 * Find the nearest toilet based on user's location and filters.
 */
public function nearest(Request $request)
{
    $validated = $request->validate([
        'latitude' => ['required', 'numeric'],
        'longitude' => ['required', 'numeric'],
    ]);

    $latitude = $validated['latitude'];
    $longitude = $validated['longitude'];

    $query = Toilet::selectRaw("
            id, name, latitude, longitude,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?)) * sin(radians(latitude)))) AS distance
        ", [$latitude, $longitude, $latitude])
        ->with(['facilities', 'discoverer'])
        ->withCount('reviews')
        ->withAvg('reviews', 'rating')
        ->orderBy('distance');

    $nearestToilet = $query->first();

    return response()->json($nearestToilet);
    }

    public function searchLocation(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $query = $request['query'];
        $latitude = $request['lat'];
        $longitude = $request['lng'];
        $apiKey = env('MAPTILER_API_KEY');
        $limit = 5;

        $response = Http::get("https://api.maptiler.com/geocoding/{$query}.json", [
            'key' => $apiKey,
            'limit' => $limit,
            'proximity' => "{$longitude},{$latitude}",
            'types' => 'poi'
        ]);

        if ($response->failed()) {
            $errorDetails = $response->json()['message'] ?? 'No error details provided.';
            return response()->json([
                'error' => 'Unable to fetch locations at the moment.',
                'details' => $errorDetails,
            ], $response->status());
        }

        $locations = collect($response->json()['features'] ?? [])
            ->filter(function ($feature) use ($latitude, $longitude) {
                $coords = $feature['geometry']['coordinates'];
                $distance = $this->haversineDistance($latitude, $longitude, $coords[1], $coords[0]);
                return $distance <= 5;
            })
            ->map(function ($feature) {
                return [
                    'coordinates' => $feature['geometry']['coordinates'],
                    'place_name' => $feature['place_name'],
                    'categories' => $feature['properties']['categories'] ?? []
                ];
            })
            ->values();

        return response()->json($locations);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
