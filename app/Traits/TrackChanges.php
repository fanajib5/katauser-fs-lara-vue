<?php

namespace App\Traits;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\{Auth, Schema};

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
            'version',
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
                $changed = array_diff_key($model->getDirty(), array_flip($model->auditIgnore()));

                if (empty($changed)) {
                    return;
                }

                $before = json_encode(array_intersect_key($model->getOriginal(), $changed));
                $after = json_encode($changed);

                AuditTrail::create([
                    'model_type' => $model->getMorphClass(),
                    'model_id' => $model->getKey(),
                    'user_id' => Auth::id(),
                    'before' => $before,
                    'after' => $after,
                ]);
            }

            // Tambahkan versi jika kolomnya ada
            if ($model->hasVersionColumn()) {
                $model->version = ($model->version ?? 0) + 1;
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });
    }

    /**
     * Mengecek apakah model memiliki kolom 'version' di tabelnya.
     * Cache disimpan per class model agar tidak query berulang.
     */
    protected function hasVersionColumn(): bool
    {
        static $cache = [];

        $class = static::class;

        if (!isset($cache[$class])) {
            $cache[$class] = Schema::hasColumn($this->getTable(), 'version');
        }

        return $cache[$class];
    }

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
}
