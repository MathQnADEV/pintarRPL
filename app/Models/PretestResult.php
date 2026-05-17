<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'score', 'level', 'completed_at'])]
class PretestResult extends Model
{
    use SoftDeletes;

    /** Urutan level dari terendah ke tertinggi. */
    public const LEVELS = ['pemula', 'menengah', 'lanjut'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung level efektif pengguna.
     *
     * Mulai dari level awal (pre-test), lalu naik satu level setiap kali
     * post-test untuk level tersebut dinyatakan lulus (passed = true).
     * Berhenti naik jika post-test belum/tidak lulus, atau sudah di level tertinggi.
     *
     * Contoh:
     *   pre-test = pemula, post-test pemula lulus  → menengah
     *   pre-test = pemula, post-test pemula + menengah lulus → lanjut
     *   pre-test = menengah, post-test menengah lulus → lanjut
     */
    public static function effectiveLevel(int $userId): string
    {
        $pretest = static::where('user_id', $userId)->latest()->first();
        $base    = $pretest?->level ?? 'pemula';
        $idx     = array_search($base, self::LEVELS, true);

        if ($idx === false) {
            $idx = 0;
        }

        $effective = $base;

        for ($i = $idx; $i < count(self::LEVELS) - 1; $i++) {
            $passed = PostTestResult::where('user_id', $userId)
                ->where('from_level', self::LEVELS[$i])
                ->where('passed', true)
                ->exists();

            if ($passed) {
                $effective = self::LEVELS[$i + 1];
            } else {
                break; // Hentikan kenaikan jika post-test level ini belum lulus
            }
        }

        return $effective;
    }
}
