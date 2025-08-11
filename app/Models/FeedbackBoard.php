<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackBoard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'public_id',
        'name',
        'description',
        'slug',
        'set_to_public_at',
        'version',
        'organization_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function isPublic(): bool
    {
        return $this->set_to_public_at !== null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
