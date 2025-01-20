<?php

declare(strict_types=1);

use App\Enums\EventType;
use App\Models\Activity;
use App\Models\Project;
use Pest\Expectation;

test('to array', function () {
    $activity = Activity::factory()->create()->refresh();

    expect(array_keys($activity->toArray()))
        ->toBe([
            'id',
            'project_id',
            'events',
            'created_at',
            'updated_at',
        ]);
});

it('belongs to a project', function () {
    $activity = Activity::factory()->create();

    expect($activity->project)->toBeInstanceOf(Project::class);
});

it('casts events to array', function () {
    $path = '/'.fake()->slug();
    $seconds = fake()->numberBetween(2, 30);

    $activity = Activity::factory()->state([
        'events' => [
            EventType::view($path),
            EventType::viewDuration($path, $seconds),
        ],
    ])->create();

    expect($activity->events)->toBeArray()->toHaveCount(2)
        ->sequence(
            fn (Expectation $event) => $event->toBe([
                'type' => 'view',
                'payload' => [
                    'url' => $path,
                ],
            ]),
            fn (Expectation $event) => $event->toBe([
                'type' => 'view_duration',
                'payload' => [
                    'url' => $path,
                    'seconds' => (string) $seconds,
                ],
            ]),
        );
});
