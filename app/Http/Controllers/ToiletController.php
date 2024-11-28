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
        $toilet = Toilet::with(['facilities', 'discoverer'])->inRandomOrder()->first();
        sleep(5); // Consider removing or handling asynchronously
        return response()->json($toilet);
    }
}
