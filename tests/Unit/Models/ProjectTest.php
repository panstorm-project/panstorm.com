<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\QueryException;

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

it('may have pages', function () {
    $project = Project::factory()->hasPages(3)->create();

    expect($project->pages)->toHaveCount(3);
});

it('has activities', function () {
    $page = Project::factory()->hasActivities(3)->create();

    expect($page->activities)->toHaveCount(3);
});

it('two users may contain projects with the same name', function () {
    $projectA = Project::factory()->create(['name' => 'My Project']);
    $projectB = Project::factory()->create(['name' => 'My Project']);

    expect($projectA->name)->toBe('My Project')
        ->and($projectB->name)->toBe('My Project');
});

test('the same user may not contain projects with the same name', function () {
    $user = User::factory()->create();

    Project::factory()
        ->for($user)
        ->count(2)
        ->create(['name' => 'My Project']);
})->throws(QueryException::class);
