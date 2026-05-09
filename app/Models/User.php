<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone', 'photo', 'nim', 'nip', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasRole('admin'),
            'dosen' => $this->hasRole('dosen'),
            default => false,
        };
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relasi sebagai dosen — kelas yang diajarkan
    public function kelasDiajar(): HasMany
    {
        return $this->hasMany(Kelas::class, 'dosen_id');
    }

    // Relasi sebagai mahasiswa — kelas yang diikuti
    // Nama "kelas" dibutuhkan oleh Filament AttachAction (inverse dari Kelas::mahasiswa())
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'class_enrollments', 'user_id', 'class_id');
    }

    public function pretestResults(): HasMany
    {
        return $this->hasMany(PretestResult::class);
    }

    public function postTestResults(): HasMany
    {
        return $this->hasMany(PostTestResult::class);
    }

    public function learningProgress(): HasMany
    {
        return $this->hasMany(LearningProgress::class);
    }

    public function quizResults(): HasMany
    {
        return $this->hasMany(QuizResult::class);
    }
}
