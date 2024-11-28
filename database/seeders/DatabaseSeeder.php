<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Review;
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
        $this->call(DefaultSeeder::class);

        // Create 20 users
        $users = User::factory(20)->create();

        // Admin user
        $adminUser = User::factory()->create([
            'username' => 'Admin',
            'email' => 'Admin@Toilet.com',
            'password' => bcrypt('12345'),
        ]);

        // Get all facility IDs
        $facilityIds = Facility::pluck('id')->toArray();

        $faker = \Faker\Factory::create();

        // Create 100 toilets in Bangkok with random facilities
        $toilets = Toilet::factory(100)->create()->each(function ($toilet) use ($adminUser, $faker, $facilityIds) {
            // Assign random discoverer
            $toilet->discoverer_id = $adminUser->id;

            // Random latitude and longitude within Bangkok's vicinity
            $toilet->latitude = 13.7563 + $faker->randomFloat(5, -0.05, 0.05); // ~5 km radius
            $toilet->longitude = 100.5018 + $faker->randomFloat(5, -0.05, 0.05);

            // Save toilet after updating location
            $toilet->save();

            // Attach random facilities
            $assignedFacilities = $faker->randomElements($facilityIds, rand(1, 3));
            $toilet->facilities()->attach($assignedFacilities);
        });

        // Create 10 reviews for each toilet
        $toilets->each(function ($toilet) use ($users, $faker) {
            Review::factory(10)->create([
                'toilet_id' => $toilet->id,
                'reviewer_id' => $faker->randomElement($users)->id,
                'gender_id' => rand(1, 3), // Assuming genders are seeded as Male, Female, Handicap
                'content' => $faker->sentence(),
                'rating' => rand(1, 5), // Random rating between 1 and 5
            ]);
        });
    }
}
