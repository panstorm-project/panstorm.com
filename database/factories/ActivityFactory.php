<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EventType;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
final class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = '/'.fake()->slug();

        return [
            'project_id' => Project::factory(),
            'events' => [
                EventType::view($path),
                EventType::viewDuration($path, fake()->numberBetween(2, 30)),
            ],
        ];
    }
}
