<?php

namespace App\Traits;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait TracksChanges
{
    /**
     * Field yang tidak akan dicatat perubahannya.
     *
     * @return string[]
     */
    protected function auditIgnore(): array
    {
        return [
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    public static function bootTracksChanges(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
            $model->version = 1;
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }

            // Simpan versi lama ke audit trail
            if ($model->isDirty()) {
                $ignore = (new static)->auditIgnore();
                $original = collect($model->getOriginal())->except($ignore);
                $changed = collect($model->getDirty())->except($ignore);

               AuditTrail::create([
                    'model_type' => get_class($model),
                    'model_id' => $model->getKey(),
                    'user_id' => Auth::id(),
                    'before' => $original->all(),
                    'after' => $changed->all(),
                ]);
            }

            $model->version++;
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
