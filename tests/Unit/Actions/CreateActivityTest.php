<?php

declare(strict_types=1);

use App\Actions\CreateActivity;
use App\Enums\EventType;
use App\Jobs\IngestActivity;
use App\Models\Project;
use Illuminate\Support\Facades\Queue;
use Pest\Expectation;

it('creates a new activity', function () {
    Queue::fake();

    $project = Project::factory()->create();
    $action = app(CreateActivity::class);

    $action->handle($project, [
        EventType::view('/about'),
        EventType::viewDuration('/about', 2),
    ]);

    $activity = $project->activities->first();
    expect($project->activities)->toHaveCount(1)
        ->and($activity->events)->toBeArray()->toHaveCount(2)
        ->sequence(
            fn (Expectation $event) => $event->toBe([
                'type' => 'view',
                'payload' => [
                    'url' => '/about',
                ],
            ]),
            fn (Expectation $event) => $event->toBe([
                'type' => 'view_duration',
                'payload' => [
                    'url' => '/about',
                    'seconds' => '2',
                ],
            ]),
        );

    Queue::assertPushed(IngestActivity::class, 1);
});
