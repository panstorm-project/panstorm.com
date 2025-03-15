<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateActivityAction;
use App\Http\Requests\CreateActivityRequest;
use App\Models\Project;
use Illuminate\Http\Response;

final readonly class ActivityController
{
    /**
     * Store a new activity.
     */
    public function store(CreateActivityRequest $request, Project $project, CreateActivityAction $action): Response
    {
        /** @var array<int, array{type: string, payload: array<string, string>}> $events */
        $events = $request->array('events');

        $action->handle($project, $events);

        return response(status: 201);
    }
}
