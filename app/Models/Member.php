<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, TracksChanges, SoftDeletes;

    protected $fillable = [
        'public_id',
        'user_id',
        'organization_id',
        'bio',
        'avatar',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
        'disabled_by',
        'disabled_at',
    ];

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'user_id' => 'integer',
            'organization_id' => 'integer',
            'bio' => 'string',
            'avatar' => 'string',
            'version' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
            'disabled_by' => 'integer',
            'disabled_at' => 'datetime',
        ];
    }

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the user associated with this member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // Kolom FK: user_id
    }

    /**
     * Get the organization this member belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
        // Kolom FK: organization_id
    }

    /**
     * Get the user who created this member record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
        // Kolom FK: created_by
    }

    /**
     * Get the user who last updated this member record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
        // Kolom FK: updated_by
    }

    /**
     * Get the user who deleted this member record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
        // Kolom FK: deleted_by (nullable)
    }

    /**
     * Get the user who disabled this member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function disabledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disabled_by');
        // Kolom FK: disabled_by (nullable)
    }

    // ========== HAS MANY RELATIONS ==========

    /**
     * Get the comments made by this member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
        // Kolom FK di Comment: member_id
    }

    /**
     * Get the votes made by this member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
        // Kolom FK di Vote: member_id
    }

    /**
     * Get the feedback posts submitted by this member.
     * (Berdasarkan skema, member_id ada di feedback_posts)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedbackPosts(): HasMany
    {
        return $this->hasMany(FeedbackPost::class);
        // Kolom FK di FeedbackPost: member_id
    }
}
