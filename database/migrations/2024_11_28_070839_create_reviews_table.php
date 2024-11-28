<?php

use App\Models\Facility;
use App\Models\Gender;
use App\Models\Review;
use App\Models\Toilet;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(User::class, 'reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(Gender::class, 'gender_id')->constrained('genders')->cascadeOnDelete();
            $table->foreignIdFor(Toilet::class, 'toilet_id')->constrained('toilets')->cascadeOnDelete();
            $table->integer('rating');
            $table->string('content');

        });

        Schema::create('review_facility', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Review::class, 'review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignIdFor(Facility::class, 'facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('review_facility');
        Schema::dropIfExists('genders');
    }
};
