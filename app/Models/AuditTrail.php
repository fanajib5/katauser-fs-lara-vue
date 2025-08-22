<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditTrail extends Model
{
    /** @use HasFactory<\Database\Factories\AuditTrailFactory> */
    use HasFactory;

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    protected const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'user_id',
        'before',
        'after',
        'organization_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'before' => 'array', // Data disimpan sebagai JSONB
            'after' => 'array',  // Data disimpan sebagai JSONB
            'created_at' => 'datetime',
        ];
    }

    // ========== RELATIONS ==========

    /**
     * Audit trail belongs to a user (who performed the action).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\AuditTrail>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Audit trail belongs to an organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Organization, \App\Models\AuditTrail>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    // Optional: Jika ingin mendukung model polymorphic (untuk model yang diaudit)
    /**
     * Get the audited model (polymorphic relation).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope to get audit trails for a specific model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\AuditTrail>  $query
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\AuditTrail>
     */
    public function scopeForModel($query, Model $model): Builder
    {
        return $query->where('model_type', $model->getMorphClass())
                     ->where('model_id', $model->getKey());
    }

    /**
     * Scope to filter by organization.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\AuditTrail>  $query
     * @param  int  $organizationId
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\AuditTrail>
     */
    public function scopeInOrganization($query, int $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }
}
