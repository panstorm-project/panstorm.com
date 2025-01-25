<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;

it('can view a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    expect((new ProjectPolicy())->view($user, $project))
        ->toBeTrue();
});

it('cannot view a project that does not belong to the user', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect((new ProjectPolicy())->view($user, $project))
        ->toBeFalse();
});
