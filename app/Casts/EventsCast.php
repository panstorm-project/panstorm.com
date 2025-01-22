<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\EventType;
use App\ValueObjects\Event;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * @implements CastsAttributes<array<Event>, array<Event>>
 */
final readonly class EventsCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @return array<Event>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (is_string($value) === false) {
            throw new RuntimeException(message: 'The events must be a string.');
        }

        /** @var array{type: string, payload: array<string, string>} $decoded */
        $decoded = json_decode(json: $value, associative: true);
        $transformed = [];

        /** @var array{type: string, payload: array<string, string>} $event */
        foreach ($decoded as $event) {
            $transformed[] = new Event(
                type: EventType::from($event['type']),
                payload: $event['payload'],
            );
        }

        return $transformed;
    }

    /**
     * Transform the attribute to its underlying model values.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string|false
    {
        return json_encode(value: $value);
    }
}
