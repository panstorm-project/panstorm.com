<?php

declare(strict_types=1);

use App\Enums\EventType;
use App\Jobs\IngestActivity;
use App\Models\Activity;
use App\Models\Page;

it('can ingest activity events', function () {
    // arrange...
    $activity = Activity::factory()->create([
        'events' => [
            EventType::view('/about'),
            EventType::viewDuration('/about', 2),
            EventType::viewDuration('/about', 4),
            EventType::viewDuration('/about', 6),
            EventType::view('/about'),
            EventType::viewDuration('/about', 2),
        ],
    ]);

    $job = new IngestActivity($activity, now());

    // act...
    $job->handle();

    // assert...
    $page = Page::first();

    expect($activity->fresh())->toBeNull()
        ->and(Page::count())->toBe(1)
        ->and($page->path)->toBe('about')
        ->and($page->views)->toBe(2)
        ->and($page->average_time)->toBe(7)
        ->and($page->bucket->format('Y-m-d H:i:s'))->toBe(now()->format('Y-m-d H:00:00'));
});

it('ignore view duration event with no views', function () {
    // arrange...
    $activity = Activity::factory()->create([
        'events' => [
            EventType::viewDuration('/about', 2),
            EventType::viewDuration('/about', 4),
            EventType::viewDuration('/about', 6),
        ],
    ]);

    $job = new IngestActivity($activity, now());

    // act...
    $job->handle();

    // assert...
    $page = Page::first();

    expect($activity->fresh())->toBeNull()
        ->and(Page::count())->toBe(1)
        ->and($page->path)->toBe('about')
        ->and($page->views)->toBe(0)
        ->and($page->average_time)->toBe(0);
});

it('ignore view duration if there no view duration sent', function () {
    // arrange...
    $activity = Activity::factory()->create([
        'events' => [
            EventType::view('/about'),
        ],
    ]);

    $job = new IngestActivity($activity, now());

    // act...
    $job->handle();

    // assert...
    $page = Page::first();

    expect($activity->fresh())->toBeNull()
        ->and(Page::count())->toBe(1)
        ->and($page->path)->toBe('about')
        ->and($page->views)->toBe(1)
        ->and($page->average_time)->toBe(0);
});
