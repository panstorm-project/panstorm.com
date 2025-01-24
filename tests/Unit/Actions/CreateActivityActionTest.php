<?php

declare(strict_types=1);

use App\Actions\CreateActivityAction;
use App\Enums\EventType;
use App\Jobs\IngestActivity;
use App\Models\Project;
use App\ValueObjects\Event;
use Illuminate\Support\Facades\Queue;
use Pest\Expectation;

it('creates a new activity', function () {
    Queue::fake();

    $project = Project::factory()->create();
    $action = app(CreateActivityAction::class);

    $action->handle($project, [
        EventType::view('/about'),
        EventType::viewDuration('/about', 2),
    ]);

    $activity = $project->activities->first();
    expect($project->activities)->toHaveCount(1)
        ->and($activity->events)->toBeArray()->toHaveCount(2)
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
                        'seconds' => '2',
                    ],
                )
            ),
        );

    Queue::assertPushed(IngestActivity::class, 1);
});
