<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Toilet;
use Illuminate\Http\Request;
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
        sleep(5); // Consider removing or handling asynchronously
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
        // 'facilities' => ['array'],
    ]);

    $latitude = $validated['latitude'];
    $longitude = $validated['longitude'];
    // $facilities = $validated['facilities'] ?? [];

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

    // if (!empty($facilities)) {
    //     $query->whereHas('facilities', function ($q) use ($facilities) {
    //         $q->whereIn('id', $facilities);
    //     });
    // }

    $nearestToilet = $query->first();

    return response()->json($nearestToilet);
}
}
