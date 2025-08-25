<?php

namespace App\Traits;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\{Auth, Schema, Cache};

trait TrackChanges
{
    /**
     * Field yang tidak akan dicatat perubahannya.
     *
     * @return array<string>
     */
    protected function auditIgnore(): array
    {
        return array_merge([
            'version',
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by',
            'updated_by',
            'deleted_by',
            'remember_token',
        ], $this->getAuditIgnore() ?? []);
    }

    /**
     * Override this method in your model to add custom ignored fields
     *
     * @return array<string>|null
     */
    protected function getAuditIgnore(): ?array
    {
        return property_exists($this, 'auditIgnoreFields') ? $this->auditIgnoreFields : null;
    }

    /**
     * Boot the trait
     */
    public static function bootTrackChanges(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && $model->shouldTrackUser()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }

            if ($model->hasVersionColumn()) {
                $model->version = 1;
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && $model->shouldTrackUser()) {
                $model->updated_by = Auth::id();
            }

            // Simpan audit trail sebelum update
            $model->recordAuditTrail();

            // Increment version
            if ($model->hasVersionColumn()) {
                $model->version = ($model->version ?? 0) + 1;
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && $model->shouldTrackUser() && $model->hasDeletedByColumn()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }

            // Record deletion in audit trail
            if ($model->shouldAuditDeletes()) {
                $auditData = [
                    'model_type' => $model->getMorphClass(),
                    'model_id' => $model->getKey(),
                    'user_id' => Auth::id(),
                    'before' => $model->getAttributes(),
                    'after' => null,
                ];

                // Add organization_id if model has it
                if ($model->hasOrganizationId()) {
                    $auditData['organization_id'] = $model->organization_id;
                }

                AuditTrail::create($auditData);
            }
        });
    }

    /**
     * Record audit trail for updates
     */
    protected function recordAuditTrail(): void
    {
        if (!$this->shouldAudit() || !$this->isDirty()) {
            return;
        }

        $changed = array_diff_key($this->getDirty(), array_flip($this->auditIgnore()));

        if (empty($changed)) {
            return;
        }

        $original = array_intersect_key($this->getOriginal(), $changed);

        $auditData = [
            'model_type' => $this->getMorphClass(),
            'model_id' => $this->getKey(),
            'user_id' => Auth::id(),
            'before' => !empty($original) ? $original : null,
            'after' => $changed,
        ];

        // Add organization_id if model has it
        if ($this->hasOrganizationId()) {
            $auditData['organization_id'] = $this->organization_id;
        }

        AuditTrail::create($auditData);
    }

    /**
     * Check if this model should be audited
     */
    protected function shouldAudit(): bool
    {
        return property_exists($this, 'auditable') ? $this->auditable : true;
    }

    /**
     * Check if deletes should be audited
     */
    protected function shouldAuditDeletes(): bool
    {
        return property_exists($this, 'auditDeletes') ? $this->auditDeletes : true;
    }

    /**
     * Check if user tracking should be enabled
     */
    protected function shouldTrackUser(): bool
    {
        return property_exists($this, 'trackUser') ? $this->trackUser : true;
    }

    /**
     * Check if model has version column with caching
     */
    protected function hasVersionColumn(): bool
    {
        $cacheKey = 'table_has_version_' . $this->getTable();

        return Cache::remember($cacheKey, now()->addDay(), fn () => Schema::hasColumn($this->getTable(), 'version'));
    }

    /**
     * Check if model has deleted_by column
     */
    protected function hasDeletedByColumn(): bool
    {
        $cacheKey = 'table_has_deleted_by_' . $this->getTable();

        return Cache::remember($cacheKey, now()->addDay(), fn() => Schema::hasColumn($this->getTable(), 'deleted_by'));
    }

    /**
     * Check if model has organization_id field
     */
    protected function hasOrganizationId(): bool
    {
        return in_array('organization_id', $this->getFillable()) ||
               Schema::hasColumn($this->getTable(), 'organization_id');
    }

    /**
     * Get audit history for this model
     */
    public function auditHistory(): mixed
    {
        return $this->hasMany(AuditTrail::class, 'model_id')
                    ->where('model_type', $this->getMorphClass())
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Relationships
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope to include tracking relationships
     */
    public function scopeWithTracking($query)
    {
        return $query->with(['createdBy', 'updatedBy', 'deletedBy']);
    }

    /**
     * Get the user who last modified this record
     */
    public function getLastModifiedByAttribute()
    {
        return $this->updatedBy ?? $this->createdBy;
    }
}
