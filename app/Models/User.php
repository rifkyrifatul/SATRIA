<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'division_id', 'admin_level'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'division_id',
        'admin_level',
        'profile_photo',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Cek apakah user adalah super_admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah admin (reviewer).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah staff.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Mendapatkan label untuk admin_level.
     */
    public function getAdminLevelLabelAttribute(): string
    {
        if (!$this->admin_level) return '-';
        return match($this->admin_level) {
            'admin_1' => 'PROGAR',
            'admin_2' => 'PEKAS',
            'admin_3' => 'SETUM',
            default => strtoupper(str_replace('_', ' ', $this->admin_level)),
        };
    }

    /**
     * Cek apakah user berwenang mengelola template surat (Super Admin & Admin SETUM).
     */
    public function canManageTemplates(): bool
    {
        return $this->isSuperAdmin() || ($this->isAdmin() && $this->admin_level === 'admin_3');
    }

    // -------------------------------------------------------------------------
    // Eloquent Relations
    // -------------------------------------------------------------------------

    /**
     * Divisi staf pengguna ini (jika ada).
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(StaffDivision::class, 'division_id');
    }

    /**
     * Surat-surat yang dibuat oleh user ini.
     */
    public function createdLetters(): HasMany
    {
        return $this->hasMany(Letter::class, 'created_by');
    }

    /**
     * Surat-surat yang terakhir diupdate oleh user ini (biasanya admin).
     */
    public function updatedLetters(): HasMany
    {
        return $this->hasMany(Letter::class, 'updated_by');
    }

    /**
     * Semua log aksi yang dilakukan oleh user ini.
     */
    public function letterLogs(): HasMany
    {
        return $this->hasMany(LetterLog::class, 'user_id');
    }

    public function mailRegistryDispositions(): HasMany
    {
        return $this->hasMany(MailRegistryDisposition::class, 'user_id');
    }

    public function renbuts(): HasMany
    {
        return $this->hasMany(Renbut::class, 'user_id');
    }
}
