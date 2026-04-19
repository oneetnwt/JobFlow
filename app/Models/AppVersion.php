<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class AppVersion
 * Represents an application version synchronized from GitHub Releases.
 *
 * @property int $id
 * @property string $version
 * @property string $title
 * @property string $release_notes
 * @property bool $is_critical
 * @property bool $is_prerelease
 * @property string|null $github_release_id
 * @property Carbon|null $released_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AppVersion extends Model
{
    use HasFactory;

    /**
     * Get the database connection for the model.
     * Always use the central database connection to access app versions.
     */
    public function getConnectionName()
    {
        return config('tenancy.database.central_connection');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'version',
        'title',
        'release_notes',
        'is_critical',
        'is_prerelease',
        'github_release_id',
        'released_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_critical' => 'boolean',
            'is_prerelease' => 'boolean',
            'released_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'version';
    }

    /**
     * Scope for production releases only.
     */
    public function scopeProduction(Builder $query): void
    {
        $query->where('is_prerelease', false);
    }
}
