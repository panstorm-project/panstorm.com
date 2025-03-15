<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\IngestActivity;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

final readonly class CreateActivityAction
{
    /**
     * Handles the action.
     *
     * @param  array<int, array{type: string, payload: array<string, string>}>  $events
     */
    public function handle(Project $project, array $events): void
    {
        DB::transaction(function () use ($project, $events): void {
            $activity = $project->activities()->create([
                'events' => $events,
            ]);

            IngestActivity::dispatch($activity, now());
        });
    }
}
