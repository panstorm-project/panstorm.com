<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\EventType;

final readonly class Event
{
    /**
     * @param  array<string, string>  $payload
     */
    public function __construct(
        public EventType $type,
        public array $payload,
    ) {}
}
