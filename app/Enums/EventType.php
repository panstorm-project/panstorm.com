<?php

declare(strict_types=1);

namespace App\Enums;

use App\ValueObjects\Event;

enum EventType: string
{
    case View = 'view';
    case ViewDuration = 'view_duration';

    /**
     * Create a new view event.
     */
    public static function view(string $url): Event
    {
        return new Event(self::View, ['url' => $url]);
    }

    /**
     * Create a new view duration event.
     */
    public static function viewDuration(string $url, int $seconds): Event
    {
        return new Event(self::ViewDuration, [
            'url' => $url,
            'seconds' => (string) $seconds,
        ]);
    }
}
