<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait Auditable
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

    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            self::logAudit($model, null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            // Ambil hanya field yang berubah dan tidak di-ignore
            $changes = collect($model->getChanges())
                ->except((new static)->auditIgnore())
                ->toArray();

            if (!empty($changes)) {
                self::logAudit($model, $model->getOriginal(), $changes);
            }
        });

        static::deleted(function (Model $model) {
            self::logAudit($model, $model->getOriginal(), null);
        });
    }

    /**
     * Simpan catatan audit trail.
     */
    protected static function logAudit(Model $model, ?array $before, ?array $after): void
    {
        try {
            // Filter kolom before & after agar tidak menyimpan field yang di-ignore
            $ignore = (new static)->auditIgnore();

            $beforeFiltered = $before ? collect($before)->except($ignore)->toArray() : null;
            $afterFiltered  = $after ? collect($after)->except($ignore)->toArray() : null;

            AuditTrail::create([
                'id'         => (string) Str::uuid(),
                'model_type' => get_class($model),
                'model_id'   => $model->getKey(),
                'user_id'    => Auth::id(),
                'before'     => $beforeFiltered ?: null,
                'after'      => $afterFiltered ?: null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Jangan hentikan proses jika gagal audit
            report($e);
        }
    }
}
