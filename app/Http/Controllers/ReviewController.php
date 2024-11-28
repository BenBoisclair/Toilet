<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Gender;
use App\Models\Review;
use App\Models\Toilet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReviewController extends Controller
{
    /**
     * Show the form for creating a new review.
     */
    public function create(Toilet $toilet)
    {
        $toilet->load(['facilities', 'discoverer', 'reviews.reviewer', 'reviews.gender'])
               ->loadCount('reviews')
               ->loadAvg('reviews', 'rating');

        $facilities = Facility::all();
        $genders = Gender::all();

        return Inertia::render('WriteReview', [
            'toilet' => $toilet,
            'facilities' => $facilities,
            'genders' => $genders
        ]);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Toilet $toilet)
    {
        $attributes = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'gender' => 'nullable|integer|exists:genders,id',
            'content' => 'nullable|string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'integer|exists:facilities,id',
        ]);

        $review = Review::create([
            'content' => $attributes['content'] ?? null,
            'gender_id' => $attributes['gender'] ?? null,
            'reviewer_id' => Auth::id(),
            'rating' => $attributes['rating'],
            'toilet_id' => $toilet->id
        ]);

        if (!empty($attributes['facilities'])) {
            $review->facilities()->attach($attributes['facilities']);
        }

        return redirect()->route('toilet.show', $toilet->id)
                         ->with('success', 'Review added successfully!');
    }
}
