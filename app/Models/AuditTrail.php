<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class AuditTrail extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'audit_trails';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'uuid';

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     * Only has created_at, no updated_at
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'uuid' => 'string',
        'before' => 'array',
        'after' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'model_type',
        'model_id',
        'user_id',
        'before',
        'after',
        'created_at',
    ];

    // ========== BELONGS TO RELATIONS ==========

    /**
     * Get the user who made the changes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User,\App\Models\AuditTrail>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ========== POLYMORPHIC RELATIONS ==========

    /**
     * Get the auditable model (polymorphic relation).
     * This allows audit trail to track changes for any model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    // ========== SCOPE METHODS ==========

    /**
     * Scope query for specific model type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $modelType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForModelType($query, string $modelType): Builder
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope query for specific model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForModel($query, Model $model): Builder
    {
        return $query->where('model_type', get_class($model))
                    ->where('model_id', $model->getKey());
    }

    /**
     * Scope query for specific user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope query for recent activities
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ========== ACCESSOR METHODS ==========

    /**
     * Get the changes made (difference between before and after).
     *
     * @return array
     */
    public function getChangesAttribute(): array
    {
        $changes = [];
        $before = $this->before ?? [];
        $after = $this->after ?? [];

        // Find all changed fields
        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($allKeys as $key) {
            $oldValue = $before[$key] ?? null;
            $newValue = $after[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get the action type based on before/after values.
     */
    public function getActionAttribute(): string
    {
        if (empty($this->before) && !empty($this->after)) {
            return 'created';
        }

        if (!empty($this->before) && empty($this->after)) {
            return 'deleted';
        }

        return 'updated';
    }

    /**
     * Get human readable model name.
     */
    public function getModelNameAttribute(): string
    {
        return class_basename($this->model_type);
    }

    // ========== HELPER METHODS ==========

    /**
     * Check if this is a creation audit.
     */
    public function isCreation(): bool
    {
        return $this->action === 'created';
    }

    /**
     * Check if this is an update audit.
     */
    public function isUpdate(): bool
    {
        return $this->action === 'updated';
    }

    /**
     * Check if this is a deletion audit.
     */
    public function isDeletion(): bool
    {
        return $this->action === 'deleted';
    }

    /**
     * Get formatted changes for display.
     *
     * @return array
     */
    public function getFormattedChanges(): array
    {
        $formatted = [];
        $changes = $this->changes;

        foreach ($changes as $field => $change) {
            $formatted[] = [
                'field' => $this->formatFieldName($field),
                'old_value' => $this->formatValue($change['old']),
                'new_value' => $this->formatValue($change['new']),
            ];
        }

        return $formatted;
    }

    /**
     * Format field name for display.
     */
    private function formatFieldName(string $field): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $field));
    }

    /**
     * Format value for display.
     *
     * @param mixed $value
     * @return string
     */
    private function formatValue($value): string
    {
        if ($value === null) {
            return '(empty)';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        return (string) $value;
    }

    /**
     * Create audit trail record.
     */
    public static function createAudit(
        Model $model,
        ?int $userId = null,
        ?array $before = null,
        ?array $after = null
    ): self {
        return self::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'user_id' => $userId ?? auth()?->id(),
            'before' => $before,
            'after' => $after,
            'created_at' => now(),
        ]);
    }
}
