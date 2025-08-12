<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory, TracksChanges;

    protected $fillable = [
        'name',
        'custom_domain',
        'domain_verified_at',
        'urls',
        'version',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'domain_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'version',
    ];

    protected function casts(): array
    {
        return [
            'domain_verified_at' => 'datetime',
            'urls' => 'array',
        ];
    }

    protected function urls(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization,\App\Models\Plan>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\User>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\Member>
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\FeedbackBoard>
     */
    public function feedbackBoards(): HasMany
    {
        return $this->hasMany(FeedbackBoard::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\RoadmapItem>
     */
    public function roadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Organization,\App\Models\Changelog>
     */
    public function changelogs(): HasMany
    {
        return $this->hasMany(Changelog::class);
    }
}
