<?php

declare(strict_types=1);

use App\Enums\EventType;
use App\Models\Activity;
use App\Models\Project;
use App\ValueObjects\Event;
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
    $activity = Activity::factory()->state([
        'events' => [
            EventType::view('/about'),
            EventType::viewDuration('/about', 42),
        ],
    ])->create();

    expect($activity->events)->toBeArray()->toHaveCount(2)
        ->sequence(
            fn (Expectation $event) => $event->toEqual(
                new Event(
                    type: EventType::View,
                    payload: [
                        'url' => '/about',
                    ],
                )
            ),
            fn (Expectation $event) => $event->toEqual(
                new Event(
                    type: EventType::ViewDuration,
                    payload: [
                        'url' => '/about',
                        'seconds' => '42',
                    ],
                )
            ),
        );
});
