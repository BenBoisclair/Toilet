<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Toilet extends Model
{

    use HasFactory;

    protected $fillable = ['name', 'location', 'rating', 'description', 'longitude', 'latitude', 'discoverer_id'];

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class);
    // }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'toilet_facility');
    }

    public function discoverer() {
        return $this->belongsTo(User::class);
    }
};
