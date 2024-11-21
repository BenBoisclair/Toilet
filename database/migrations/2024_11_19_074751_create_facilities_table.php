<?php

use App\Models\Facility;
use App\Models\Toilet;
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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });

        Schema::create('toilet_facility', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Toilet::class, 'toilet_id')->constrained('toilets')->cascadeOnDelete();
            $table->foreignIdFor(Facility::class, 'facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('toilet_facility');
    }
};
