<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Toilet;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(FacilitySeeder::class);

        $adminUser = User::factory()->create([
            'username' => 'Admin',
            'email' => 'Admin@Toilet.com',
            'password' => bcrypt('12345'),
        ]);

        // $toilets = Toilet::factory(100)->create([
        //     'discoverer_id' => $adminUser->id,
        // ]);

        // $facilityIds = Facility::pluck('id')->toArray();

        // $faker = \Faker\Factory::create();
        // $toilets->each(function ($toilet) use ($facilityIds, $faker) {
        //     $assignedFacilities = $faker->randomElements($facilityIds, rand(1, 3));
        //     $toilet->facilities()->attach($assignedFacilities);
        // });

        // Toilet::all()->each(function ($toilet) {
        //     Review::factory(5)->create(['toilet_id' => $toilet->id]);
        // });
    }
}
