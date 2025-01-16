<?php

declare(strict_types=1);

use App\Models\Page;
use App\Models\Project;

test('to array', function () {
    $page = Page::factory()->create()->refresh();

    expect(array_keys($page->toArray()))
        ->toBe([
            'id',
            'project_id',
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
