<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EventsCast;
use App\ValueObjects\Event;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $project_id
 * @property-read array<int, Event> $events
 * @property-read Project $project
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    /**
     * Get the project that owns the activity.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'events' => EventsCast::class,
        ];
    }
}
