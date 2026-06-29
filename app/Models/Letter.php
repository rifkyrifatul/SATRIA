<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;

class Letter extends Model
{
    use HasFactory, HasUuids, LogsActivity, SoftDeletes, Prunable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['category_id', 'letter_number', 'title', 'status', 'created_by', 'updated_by'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'letters';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'letter_number',
        'title',
        'file_path',
        'final_file_path',
        'file_extension',
        'status',
        'last_rejected_status',
        'is_force_approved',
        'version',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    public const STATUS_PENDING_ADMIN_1 = 'pending_admin_1';
    public const STATUS_PENDING_ADMIN_2 = 'pending_admin_2';
    public const STATUS_PENDING_ADMIN_3 = 'pending_admin_3';
    public const STATUS_PENDING_KABAGUM = 'pending_kabagum';
    public const STATUS_PENDING_KASEK = 'pending_kasek';
    public const STATUS_REVISION = 'revision';
    public const STATUS_APPROVED = 'approved';

    /** Daftar semua status valid — digunakan untuk validasi & iterasi. */
    public const STATUSES = [
        self::STATUS_PENDING_ADMIN_1,
        self::STATUS_PENDING_ADMIN_2,
        self::STATUS_PENDING_ADMIN_3,
        self::STATUS_PENDING_KABAGUM,
        self::STATUS_PENDING_KASEK,
        self::STATUS_REVISION,
        self::STATUS_APPROVED,
    ];

    /** State Machine: Map transisi status yang valid. */
    public const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING_ADMIN_1 => self::STATUS_PENDING_ADMIN_2,
        self::STATUS_PENDING_ADMIN_2 => self::STATUS_PENDING_ADMIN_3,
        self::STATUS_PENDING_ADMIN_3 => self::STATUS_PENDING_KABAGUM,
        self::STATUS_PENDING_KABAGUM => self::STATUS_PENDING_KASEK,
        self::STATUS_PENDING_KASEK   => self::STATUS_APPROVED,
    ];

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Dapatkan status selanjutnya berdasarkan State Machine.
     * @throws \Exception Jika status saat ini tidak memiliki transisi yang valid.
     */
    public function getNextStatus(): string
    {
        if (!array_key_exists($this->status, self::ALLOWED_TRANSITIONS)) {
            throw new \Exception("Transisi status ilegal dari: {$this->status}");
        }
        return self::ALLOWED_TRANSITIONS[$this->status];
    }

    /**
     * Cek apakah surat masih dalam status pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_ADMIN_1,
            self::STATUS_PENDING_ADMIN_2,
            self::STATUS_PENDING_ADMIN_3,
        ]);
    }

    /**
     * Cek apakah surat sedang di tahap manual (KABAGUM / KASEK).
     */
    public function isManualPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_KABAGUM,
            self::STATUS_PENDING_KASEK,
        ]);
    }

    /**
     * Cek apakah surat sedang diminta revisi.
     */
    public function isRevision(): bool
    {
        return $this->status === self::STATUS_REVISION;
    }

    /**
     * Cek apakah surat sudah disetujui.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Mendapatkan label status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_ADMIN_1 => 'Menunggu PROGAR',
            self::STATUS_PENDING_ADMIN_2 => 'Menunggu PEKAS',
            self::STATUS_PENDING_ADMIN_3 => 'Menunggu SETUM',
            self::STATUS_PENDING_KABAGUM => 'Menunggu KABAGUM',
            self::STATUS_PENDING_KASEK   => 'Menunggu KASEK',
            self::STATUS_REVISION        => 'Revisi',
            self::STATUS_APPROVED        => 'Selesai / Disetujui',
            default                      => 'Tidak Diketahui',
        };
    }

    // -------------------------------------------------------------------------
    // Eloquent Relations
    // -------------------------------------------------------------------------

    /**
     * Kategori dari surat ini.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * User yang membuat / mengajukan surat ini.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User (admin) yang terakhir mengubah status surat ini.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Semua log aktivitas yang terkait dengan surat ini.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LetterLog::class, 'letter_id');
    }

    /**
     * Semua lampiran surat ini.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(LetterAttachment::class, 'letter_id')->orderBy('created_at', 'asc');
    }

    /**
     * Log terbaru dari surat ini.
     */
    public function latestLog(): HasMany
    {
        return $this->hasMany(LetterLog::class, 'letter_id')->latest();
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        // Eager load 'attachments' agar event forceDeleted dapat menghapus
        // semua file fisik attachment saat pruning otomatis berjalan.
        return static::onlyTrashed()
            ->with('attachments')
            ->where('deleted_at', '<=', now()->subDays(30));
    }

    /**
     * Booted method untuk mendaftarkan event listener.
     */
    protected static function booted(): void
    {
        // Event ini akan dipicu OTOMATIS saat $letter->forceDelete() 
        // dipanggil di Controller ATAU saat sistem Pruning otomatis berjalan.
        static::forceDeleted(function ($letter) {
            $files = array_filter([$letter->file_path, $letter->final_file_path]);
            
            foreach ($letter->attachments as $attachment) {
                if ($attachment->file_path) {
                    $files[] = $attachment->file_path;
                }
            }

            foreach (array_unique($files) as $file) {
                if (\Illuminate\Support\Facades\Storage::disk('private')->exists($file)) {
                    \Illuminate\Support\Facades\Storage::disk('private')->delete($file);
                }
            }
        });
    }
}
