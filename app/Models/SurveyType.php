<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyType extends Model
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
        'name',
        'description',
        'published_at',
        'version',
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
            'version' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========== RELATIONS ==========

    /**
     * Survey type belongs to an organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization, \App\Models\SurveyType>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Survey type was created by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\SurveyType>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Survey type was updated by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\SurveyType>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Survey type was deleted by a user (nullable).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\SurveyType>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ========== SCOPES ==========

    /**
     * Scope to get only published survey types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\SurveyType>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\SurveyType>
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Scope to filter by organization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\SurveyType>  $query
     * @param  int  $organizationId
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\SurveyType>
     */
    public function scopeInOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope to find by name (case-insensitive).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\SurveyType>  $query
     * @param  string  $name
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\SurveyType>
     */
    public function scopeByName($query, string $name)
    {
        return $query->whereRaw('LOWER(name) = ?', [strtolower($name)]);
    }
}
