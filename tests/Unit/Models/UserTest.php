<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;

test('to array', function () {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);
});

it('may have projects', function () {
    $user = User::factory()->hasProjects(3)->create();

    expect($user->projects)->toHaveCount(3)
        ->each->toBeInstanceOf(Project::class);
});
