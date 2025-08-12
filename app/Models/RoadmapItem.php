<?php

namespace App\Models;

use App\Enums\RoadmapItemStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class RoadmapItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_id',
        'organization_id',
        'feedback_post_id',
        'title',
        'content',
        'status',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'organization_id' => 'integer',
            'feedback_post_id' => 'integer',
            'status' => RoadmapItemStatus::class,
            'version' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the organization that owns this roadmap item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
        // Kolom FK: organization_id
    }

    /**
     * Get the feedback post associated with this roadmap item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedbackPost(): BelongsTo
    {
        return $this->belongsTo(FeedbackPost::class);
        // Kolom FK: feedback_post_id (nullable)
    }

    /**
     * Get the user who created this roadmap item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
        // Kolom FK: created_by
    }

    /**
     * Get the user who last updated this roadmap item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
        // Kolom FK: updated_by
    }

    /**
     * Get the user who deleted this roadmap item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
        // Kolom FK: deleted_by (nullable)
    }

    // ========== HAS ONE RELATIONS ==========

    // /**
    //  * Get the changelog associated with this roadmap item.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasOne
    //  */
    // public function changelog(): HasOne
    // {
    //     return $this->hasOne(Changelog::class, 'roadmap_item_id');
    //     // Kolom FK di Changelog: roadmap_item_id
    // }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get the changelogs related to this roadmap item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changelogs(): HasMany
    {
        return $this->hasMany(Changelog::class, 'roadmap_item_id');
        // Kolom FK di Changelog: roadmap_item_id
    }
}
