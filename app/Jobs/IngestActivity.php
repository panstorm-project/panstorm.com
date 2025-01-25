<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\EventType;
use App\Models\Activity;
use App\Models\Page;
use App\ValueObjects\Event;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

final class IngestActivity implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Activity $activity, private readonly CarbonImmutable $bucket)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $events = $this->activity->events;

        // Group events by URL and bucket to process them together
        collect($events)
            ->groupBy(fn (Event $event) => $this->urlToPath($event->payload['url']))
            ->each(function (Collection $urlEvents, string $path): void {
                $bucket = $this->bucket->setTime($this->bucket->hour, 0, 0);

                /** @var Page $page */
                $page = $this->activity->project->pages()->firstOrCreate([
                    'path' => $path,
                    'bucket' => $bucket,
                ], [
                    'views' => 0,
                    'average_time' => 0,
                ]);

                // Count views and sum durations
                $viewCount = $urlEvents->filter(fn (Event $event) => $event->type === EventType::View)->count();
                $durationCollection = $urlEvents
                    ->filter(fn (Event $event) => $event->type === EventType::ViewDuration)
                    ->map(fn (Event $event) => (int) $event->payload['seconds']);

                // Update page with aggregated stats
                $this->updatePageStats($page, $viewCount, $durationCollection);
            });

        $this->activity->delete();
    }

    private function updatePageStats(Page $page, int $newViews, Collection $durationCollection): void
    {
        $oldViews = $page->views;
        $oldAverage = $page->average_time;
        $totalViews = $oldViews + $newViews;

        // Calculate new average time
        $newAverage = $totalViews > 0
            ? (($oldAverage * $oldViews) + $durationCollection->sum()) / $totalViews
            : 0;

        $page->update([
            'views' => $totalViews,
            'average_time' => $newAverage,
        ]);
    }

    /**
     * Convert a URL to a path.
     */
    private function urlToPath(string $url): string
    {
        return mb_trim((string) parse_url($url, PHP_URL_PATH), '/');
    }
}
