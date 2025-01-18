<?php

declare(strict_types=1);

use App\Jobs\IngestActivity;
use App\Models\Activity;

it('can ingest activity events', function () {
    $activity = Activity::factory()->create();

    $job = new IngestActivity($activity);

    $job->handle();
})->expectNotToPerformAssertions();
