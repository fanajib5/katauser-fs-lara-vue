<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changelog extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /** @use TracksChanges<\App\Traits\TracksChanges> */
    use TracksChanges;

    /** @use SoftDeletes<\Illuminate\Database\Eloquent\SoftDeletes> */
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'public_id',
        'organization_id',
        'roadmap_item_id',
        'title',
        'content',
        'version',
        'published_at',
        'published_by',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'public_id' => 'uuid',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========== RELATIONS ==========

    /**
     * Changelog belongs to an organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization, \App\Models\Changelog>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Changelog belongs to a roadmap item (optional).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\RoadmapItem, \App\Models\Changelog>
     */
    public function roadmapItem(): BelongsTo
    {
        return $this->belongsTo(RoadmapItem::class, 'roadmap_item_id');
    }

    /**
     * Changelog was published by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Changelog>
     */
    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Changelog was created by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Changelog>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Changelog was updated by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Changelog>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Changelog was deleted by a user (nullable).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Changelog>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ========== SCOPES ==========

    /**
     * Scope to get published changelogs only.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Changelog>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Changelog>
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Scope to filter by organization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Changelog>  $query
     * @param  int  $organizationId
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Changelog>
     */
    public function scopeInOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope to get changelogs published after a certain date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Changelog>  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Changelog>
     */
    public function scopePublishedAfter($query, string $date)
    {
        return $query->where('published_at', '>=', $date);
    }
}
