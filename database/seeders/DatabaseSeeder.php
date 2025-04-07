<?php

namespace Database\Seeders;

use App\Models\JobSource;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        JobSource::factory()->create([
            'source_name' => 'MyJobMag',
            //'channel_or_group' => 'MyJobMag',
            //'requires_third_party_login' => false,
            //'no_of_applications' => 0,
            //'last_crawled' => now(),
            //'no_of_jobs' => 0,
        ]);

        JobSource::factory()->create([
            'source_name' => 'HotNigerianJobs',
        ]);

        JobSource::factory()->create([
            'source_name' => 'Jobberman',
            //'channel_or_group' => 'Jobberman',
            //'requires_third_party_login' => false,
            //'no_of_applications' => 0,
            //'last_crawled' => now(),
            //'no_of_jobs' => 0,
        ]);

        JobSource::factory()->create([
            'source_name' => 'JobGurus',
            //'channel_or_group' => 'JobGurus',
            //'requires_third_party_login' => false,
            //'no_of_applications' => 0,
            //'last_crawled' => now(),
            //'no_of_jobs' => 0,
        ]);
    }
}
