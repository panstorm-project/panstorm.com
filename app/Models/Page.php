<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 *
 * @propert int $project_id
 *
 * @property string $path
 * @property CarbonInterface $bucket
 * @property int $views
 * @property int $average_time
 * @property Project $project
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 */
final class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    /**
     * Get the project that owns the page.
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
            'bucket' => 'datetime',
            'views' => 'integer',
            'average_time' => 'integer',
        ];
    }
}
