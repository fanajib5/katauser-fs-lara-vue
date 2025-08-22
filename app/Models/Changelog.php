<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changelog extends Model
{
    use TracksChanges, SoftDeletes;

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

    // Casting kolom
    public function casts(): array
    {
        return [
            'public_id' => 'string',
            'organization_id' => 'integer',
            'roadmap_item_id' => 'integer',
            'title' => 'string',
            'content' => 'string',
            'version' => 'string',
            'published_at' => 'datetime',
            'published_by' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    /**
     * Changelog belongs to an organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization,\App\Models\Changelog>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Changelog belongs to a roadmap item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\RoadmapItem,\App\Models\Changelog>
     */
    public function roadmapItem(): BelongsTo
    {
        return $this->belongsTo(RoadmapItem::class);
    }

    /**
     * Returns the user who published the changelog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\Changelog>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Get the user who created the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
