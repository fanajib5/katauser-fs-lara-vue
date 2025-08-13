<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackBoard extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $fillable = [
        'public_id',
        'organization_id',
        'name',
        'slug',
        'description',
        'set_to_public_at',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function casts(): array
    {
        return [
            'public_id' => 'string',
            'organization_id' => 'integer',
            'set_to_public_at' => 'datetime',
            'version' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    // ========== ACCESSOR ==========

    public function isPublic(): bool
    {
        return $this->set_to_public_at !== null;
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the organization that owns the feedback board.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization,\App\Models\FeedbackBoard>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who created the feedback board.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\FeedbackBoard>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the feedback board.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\FeedbackBoard>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the feedback board.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\FeedbackBoard>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get the feedback posts for the feedback board.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\FeedbackPost>
     */
    public function feedbackPosts(): HasMany
    {
        return $this->hasMany(FeedbackPost::class, 'feedback_board_id');
    }
}
