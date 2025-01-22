<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use App\Casts\EventsCast;
use App\ValueObjects\Event;
use App\Models\Activity;
use App\Enums\EventType;

beforeEach(function () {
    $this->cast = new EventsCast();
});

it('transforms JSON to an array of Event ValueObject on get', function () {
    $transformed = $this->cast->get(
        model: new Activity(),
        key: 'events',
        value: json_encode([
            [
                'type' => 'view',
                'payload' => [
                    'url' => '/about',
                ],
            ],
        ]),
        attributes: []
    );

    expect($transformed)->toBeArray()
        ->and($transformed)->toHaveCount(1)
        ->and($transformed[0])->toBeInstanceOf(Event::class)
        ->and($transformed[0]->type)->toBe(EventType::View)
        ->and($transformed[0]->payload)->toBeArray()
        ->and($transformed[0]->payload)->toMatchArray([
            'url' => '/about',
        ]);
});

it('transforms Event ValueObject to JSON on set', function () {
    $result = $this->cast->set(
        model: new Activity(),
        key: 'events',
        value: [
            new Event(
                type: EventType::View,
                payload: [
                    'url' => '/about',
                ],
            ),
        ],
        attributes: []
    );

    expect($result)->toBeJson();

    $decoded = json_decode($result, true);

    expect($decoded)->toHaveCount(1)
        ->and($decoded[0])->toMatchArray([
            'type' => 'view',
            'payload' => [
                'url' => '/about',
            ],
        ]);
});

it('throws exception on get when value is not a string', function () {
    expect(fn () => $this->cast->get(
        model: new Activity(),
        key: 'events',
        value: new stdClass(),
        attributes: []
    ))->toThrow(RuntimeException::class, 'The events must be a string.');
});
