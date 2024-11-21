<?php

use App\Http\Controllers\ProfileController;
use App\Models\Facility;
use App\Models\Toilet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $toilets = Toilet::with('facilities', 'discoverer')->get();
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

Route::get('/toilets/{toilet}', function (Toilet $toilet) {
    $toilet->load('facilities');
    return Inertia::render('Toilet/Show', ['toilet' => $toilet]);
});

Route::get('/toilet/random', function () {
    $toilet = Toilet::with('facilities')->inRandomOrder()->first();
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
