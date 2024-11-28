<?php

use App\Http\Controllers\ProfileController;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Review;
use App\Models\Toilet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $toilets = Toilet::with('facilities', 'discoverer')
    ->withCount('reviews')
    ->withAvg('reviews', 'rating')
    ->get();

    // dd($toilets[10]);
    return Inertia::render('Map', ['toilets' => $toilets]);
})->name('map');

Route::get('/toilets/add', function () {
    $facilities = Facility::all();
    return Inertia::render('Toilet/Create', ['facilities' => $facilities]);
})->middleware(['auth'])->name('add-toilet');

Route::post('/toilets/add', function () {
    $attributes = request()->validate([
        'name' => ['required', 'string', 'min:2'],
        'latitude' => ['required'],
        'longitude' => ['required'],
        'description' => ['required', 'string'],
        'facilities' => ['array']
    ]);

    $attributes['discoverer_id'] = Auth::id();
    $toilet = Toilet::create($attributes);
    $toilet->facilities()->attach($attributes['facilities']);
    return redirect('/');
})->middleware(['auth'])->name('store-toilet');

Route::get('/toilets/{toilet}/reviews/add', function (Toilet $toilet) {
    $toilet->load('facilities', 'discoverer', 'reviews.reviewer', 'reviews.gender')->loadCount('reviews')
    ->loadAvg('reviews', 'rating');

    $facilities = Facility::all();

    $genders = Gender::all();

    // dd($toilet['reviews'][1]['reviewer']);
    return Inertia::render('WriteReview', ['toilet' => $toilet, 'facilities' => $facilities, 'genders' => $genders]);
});

Route::post('/toilets/{toilet}/reviews/add', function (Request $request, Toilet $toilet) {
    $attributes = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'gender' => 'nullable|integer|exists:genders,id',
        'content' => 'nullable|string|max:255',
        'facilities' => 'nullable|array',
        'facilities.*' => 'integer|exists:facilities,id',
    ]);

    $review = Review::create([
        'content' => $attributes['content'],
        'gender_id' => $attributes['gender'],
        'reviewer_id' => Auth::id(),
        'rating' => $attributes['rating'],
        'toilet_id' => $toilet['id']
    ]);

    if (!empty($validated['facilities'])) {
        $review->facilities()->attach($attributes['facilities']);
    }

    return redirect("/toilets/{$toilet->id}");
});

Route::get('/toilets/{toilet}', function (Toilet $toilet) {
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

    // dd($toilet['reviews'][1]['reviewer']);
    return Inertia::render('Toilet/Show', ['toilet' => $toilet]);
});

Route::get('/toilet/random', function () {
    $toilet = Toilet::with('facilities', 'discoverer')->inRandomOrder()->first();
    sleep(5);

    return response()->json($toilet);;
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
