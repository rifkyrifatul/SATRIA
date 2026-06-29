<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'letter_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'letter_id',
        'action',
        'notes',
        'user_id',
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

    public const ACTION_SUBMIT           = 'submit';
    public const ACTION_APPROVE          = 'approve';
    public const ACTION_REQUEST_REVISION = 'request_revision';
    public const ACTION_FORWARD_KASEK    = 'forward_kasek';
    public const ACTION_MARK_FINISHED    = 'mark_finished';

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Mendapatkan label aksi dalam Bahasa Indonesia.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_SUBMIT           => 'Pengajuan Surat',
            self::ACTION_APPROVE          => 'Surat Disetujui',
            self::ACTION_REQUEST_REVISION => 'Permintaan Revisi',
            self::ACTION_FORWARD_KASEK    => 'Diteruskan ke KASEK',
            self::ACTION_MARK_FINISHED    => 'Surat Selesai (Final)',
            default                       => 'Aksi Tidak Diketahui',
        };
    }

    // -------------------------------------------------------------------------
    // Eloquent Relations
    // -------------------------------------------------------------------------

    /**
     * Surat yang terkait dengan log ini.
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class, 'letter_id');
    }

    /**
     * User yang melakukan aksi pada log ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
