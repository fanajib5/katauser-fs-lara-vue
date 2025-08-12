<?php

namespace App\Models;

use App\Enums\VoteType;
use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $fillable = [
        'feedback_post_id',
        'member_id',
        'type',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected function casts(): array
    {
        return [
            'feedback_post_id' => 'integer',
            'member_id' => 'integer',
            'type' => VoteType::class,
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the feedback post that owns this vote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedbackPost(): BelongsTo
    {
        return $this->belongsTo(FeedbackPost::class);
        // Kolom FK: feedback_post_id
    }

    /**
     * Get the member who cast this vote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
        // Kolom FK: member_id
    }

    /**
     * Get the user who created this vote record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
        // Kolom FK: created_by
    }

    /**
     * Get the user who last updated this vote record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
        // Kolom FK: updated_by
    }

    /**
     * Get the user who deleted this vote record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
        // Kolom FK: deleted_by (nullable)
    }

    // ========== SCOPES ==========

    /**
     * Scope to filter upvotes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpvotes($query): Builder
    {
        return $query->where('type', VoteType::UPVOTE); // Gunakan enum
    }

    /**
     * Scope to filter downvotes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDownvotes($query): Builder
    {
        return $query->where('type', VoteType::DOWNVOTE); // Gunakan enum
    }

    // ========== ACCESSORS ==========

    /**
     * Check if this is an upvote.
     *
     * @return bool
     */
    public function getIsUpvoteAttribute(): bool
    {
        return $this->type === VoteType::UPVOTE; // Bandingkan dengan enum, bukan value
    }

    /**
     * Check if this is a downvote.
     *
     * @return bool
     */
    public function getIsDownvoteAttribute(): bool
    {
        return $this->type === VoteType::DOWNVOTE; // Bandingkan dengan enum, bukan value
    }
}
