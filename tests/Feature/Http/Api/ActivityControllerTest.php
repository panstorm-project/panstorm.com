<?php

declare(strict_types=1);

use App\Enums\EventType;
use App\Jobs\IngestActivity;
use App\Models\Project;
use Illuminate\Support\Facades\Queue;

beforeEach()->only();

it('can create an activity', function () {
    // Arrange...
    Queue::fake([IngestActivity::class]);
    $project = Project::factory()->create()->fresh();

    $events = [
        EventType::view('/about'),
    ];

    // Act...
    $response = $this->postJson(route('api.activities.store', $project), [
        'events' => $events,
    ]);

    // Assert...
    $response->assertStatus(201);

    $activities = $project->activities;
    expect($activities)->toHaveCount(1);

    Queue::assertPushed(IngestActivity::class, 1);
});

it('does not handle empty events', function () {
    // Arrange...
    Queue::fake([IngestActivity::class]);
    $project = Project::factory()->create()->fresh();

    // Act...
    $response = $this->postJson(route('api.activities.store', $project), [
        'events' => [],
    ]);

    // Assert...
    $response->assertStatus(422)->assertJsonValidationErrors([
        'events' => 'The events field is required.',
    ]);

    $activities = $project->activities;
    expect($activities)->toHaveCount(0);

    Queue::assertNotPushed(IngestActivity::class);
});

it('does not handle corrupted events', function (array $payload, string $message) {
    // Arrange...
    Queue::fake([IngestActivity::class]);
    $project = Project::factory()->create()->fresh();

    // Act...
    $response = $this->postJson(route('api.activities.store', $project), $payload);

    // Assert...
    $response->assertStatus(422)->assertJsonFragment([
        'message' => $message,
    ]);

    $activities = $project->activities;
    expect($activities)->toHaveCount(0);

    Queue::assertNotPushed(IngestActivity::class);
})->with([
    'empty array' => [
        [],
        'The events field is required.',
    ],
    'missing type' => [
        [
            'events' => [
                [
                    'payload' => [
                        'url' => '/about',
                    ],
                ],
            ],
        ],
        'The events.0.type field is required.',
    ],
    'missing payload' => [
        [
            'events' => [
                [
                    'type' => EventType::ViewDuration,
                ],
            ],
        ],
        'The events.0.payload field is required.',
    ],
    'missing url when the event type is view' => [
        [
            'events' => [
                [
                    'type' => EventType::View,
                    'payload' => [
                        'missing' => 'url',
                    ],
                ],
            ],
        ],
        'The events.0.payload.url field is required when events.0.type is view.',
    ],
]);
