<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $guarded = [];

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function gender() {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'review_facility');
    }

}
