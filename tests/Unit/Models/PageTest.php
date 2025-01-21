<?php

declare(strict_types=1);

use App\Models\Page;
use App\Models\Project;
use Carbon\CarbonImmutable;

test('to array', function () {
    $page = Page::factory()->create()->refresh();

    expect(array_keys($page->toArray()))
        ->toBe([
            'id',
            'project_id',
            'path',
            'bucket',
            'views',
            'average_time',
            'created_at',
            'updated_at',
        ]);
});

it('belongs to a project', function () {
    $page = Page::factory()->create();

    expect($page->project)->toBeInstanceOf(Project::class);
});

it('has a bucket date value', function () {
    $page = Page::factory()->create([
        'bucket' => '2021-01-01 01:00:00',
    ])->fresh();

    expect($page->bucket)->toBeInstanceOf(CarbonImmutable::class)
        ->and($page->bucket->format('Y-m-d H:i:s'))->toBe('2021-01-01 01:00:00');
});
