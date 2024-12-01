<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ToiletController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [ToiletController::class, 'index'])->name('map');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Toilet Routes
    Route::prefix('toilets')->group(function () {
        Route::get('/add', [ToiletController::class, 'create'])->name('add-toilet');
        Route::post('/add', [ToiletController::class, 'store'])->name('store-toilet');

        // Review Routes
        Route::prefix('{toilet}/reviews')->group(function () {
            Route::get('/add', [ReviewController::class, 'create'])->name('reviews.create');
            Route::post('/add', [ReviewController::class, 'store'])->name('reviews.store');
        });
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/toilets/{toilet}/get', [ToiletController::class, 'getToilet'])->name('toilet.get');
Route::get('/toilets/{toilet}', [ToiletController::class, 'show'])->name('toilet.show');
Route::get('/toilet/random', [ToiletController::class, 'random'])->name('toilet.random');
Route::get('/toilet/nearest', [ToiletController::class, 'nearest'])->name('toilet.nearest');
Route::get('/toilet/search/', [ToiletController::class, 'searchToilet'])->name('location.search');
Route::get('/toilet/search/poi', [ToiletController::class, 'searchPOI'])->name('location.search');


Route::get('/dashboard', function () {
    return inertia('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';
