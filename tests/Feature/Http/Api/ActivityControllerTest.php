<?php

declare(strict_types=1);

use App\Enums\EventType;
use App\Jobs\IngestActivity;
use App\Models\Project;
use Illuminate\Support\Facades\Queue;

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

it('does not handle corrupted events', function () {
    // Arrange...
    Queue::fake([IngestActivity::class]);
    $project = Project::factory()->create()->fresh();

    // Act...
    $response = $this->postJson(route('api.activities.store', $project), [
        'events' => [
            1,
            'string',
            [
                1,
            ],
            [
                'type' => 'view',
            ],
            [
                'type' => 'view',
                'payload' => [
                    //
                ],
            ],
        ],
    ]);

    // Assert...
    $response->assertStatus(422)->assertJson([
        'message' => 'The events.0.type field is required. (and 7 more errors)',
        'errors' => [
            'events.0.type' => ['The events.0.type field is required.'],
            'events.1.type' => ['The events.1.type field is required.'],
            'events.2.type' => ['The events.2.type field is required.'],
            'events.0.payload' => ['The events.0.payload field is required.'],
            'events.1.payload' => ['The events.1.payload field is required.'],
            'events.2.payload' => ['The events.2.payload field is required.'],
            'events.3.payload' => ['The events.3.payload field is required.'],
            'events.4.payload' => ['The events.4.payload field is required.'],
        ],
    ]);

    $activities = $project->activities;
    expect($activities)->toHaveCount(0);

    Queue::assertNotPushed(IngestActivity::class);
});
