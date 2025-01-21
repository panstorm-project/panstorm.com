<?php

declare(strict_types=1);

use App\Enums\EventType;
use App\Jobs\IngestActivity;
use App\Models\Activity;
use App\Models\Page;
use Illuminate\Support\Facades\Date;

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

it('handles well visits on different buckets', function () {
    // arrange...
    $activityA = Activity::factory()->create([
        'events' => [
            EventType::view('/about'),
        ],
    ]);

    $activityB = Activity::factory()->create([
        'events' => [
            EventType::view('/about'),
            EventType::view('/about'),
        ],
    ]);

    $jobA = new IngestActivity(
        $activityA,
        Date::create(2025, 1, 1, 1, 10, 0)
    );
    $jobB = new IngestActivity(
        $activityB,
        Date::create(2025, 1, 1, 2, 30, 0)
    );

    // act...
    $jobA->handle();
    $jobB->handle();

    // assert...
    [$pageA, $pageB] = Page::all();

    expect($activityA->fresh())->toBeNull()
        ->and($activityB->fresh())->toBeNull()
        ->and(Page::count())->toBe(2)
        ->and($pageA->path)->toBe('about')
        ->and($pageA->views)->toBe(1)
        ->and($pageA->average_time)->toBe(0)
        ->and($pageA->bucket->format('Y-m-d H:i:s'))->toBe('2025-01-01 01:00:00')
        ->and($pageB->path)->toBe('about')
        ->and($pageB->views)->toBe(2)
        ->and($pageB->average_time)->toBe(0)
        ->and($pageB->bucket->format('Y-m-d H:i:s'))->toBe('2025-01-01 02:00:00');
});
