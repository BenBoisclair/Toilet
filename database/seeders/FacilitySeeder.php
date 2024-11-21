<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = ['Bidet', 'Toilet Paper', 'Air Conditioner'];
        foreach ($facilities as $facilityName) {
            Facility::create(['name' => $facilityName]);
        }
    }
}
