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

    /**
     * Instantiate the Event ValueObject from an array.
     *
     * @param  array{type: string, payload: array<string, string>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: EventType::from($data['type']),
            payload: $data['payload'],
        );
    }
}
