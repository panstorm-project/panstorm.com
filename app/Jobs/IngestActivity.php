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

final class IngestActivity implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Activity $activity, private CarbonImmutable $bucket)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $events = $this->activity->events;

        collect($events)
            ->each(function (Event $event): void {
                $path = $this->urlToPath($event->payload['url']);
                $bucket = $this->bucket->setTime($this->bucket->hour, 0, 0);

                $page = $this->activity->project->pages()->firstOrCreate([
                    'path' => $path,
                    'bucket' => $bucket,
                ], [
                    'views' => 0,
                    'average_time' => 0,
                ]);

                match ($event->type) {
                    EventType::View => $this->handleView($page),
                    EventType::ViewDuration => $this->handleViewDuration($page, $event),
                };
            });

        $this->activity->delete();
    }

    /**
     * Handle the view event.
     */
    private function handleView(Page $page): void
    {
        $averageTime = $page->average_time;
        $views = $page->views + 1;

        $page->update([
            'views' => $views,
            'average_time' => $averageTime === 0
                ? 0
                : $averageTime / $views,
        ]);
    }

    /**
     * Handle the view duration event.
     */
    private function handleViewDuration(Page $page, Event $event): void
    {
        $views = $page->views;

        if ($views === 0) {
            return;
        }

        $averageTime = $page->average_time;
        $seconds = (int) $event->payload['seconds'];
        $page->update([
            'average_time' => $averageTime === 0
                ? $seconds
                : ($averageTime * $views + $seconds) / $views,
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
