<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;

test('to array', function () {
    $project = Project::factory()->create()->refresh();

    expect(array_keys($project->toArray()))
        ->toBe([
            'id',
            'user_id',
            'name',
            'created_at',
            'updated_at',
        ]);
});

it('belongs to a user', function () {
    $project = Project::factory()->create();

    expect($project->user)->toBeInstanceOf(User::class);
});
