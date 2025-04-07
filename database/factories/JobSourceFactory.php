<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobSource>
 */
class JobSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_name' => 'MyJobMag',
            //'channel_or_group',
            //'requires_third_party_login',
            //'third_party_name',
            //'no_of_applications',
        ];
    }
}
