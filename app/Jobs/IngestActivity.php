<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Activity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class IngestActivity implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Activity $activity) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->activity->save();
    }
}
