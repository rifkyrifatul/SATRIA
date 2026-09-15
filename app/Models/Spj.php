<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spj extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'spj_number',
        'type',
        'file_path',
        'file_original_name',
        'file_size',
        'date',
        'description',
        'admin_level',
        'uploaded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * User / Admin yang mengunggah berkas SPJ.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Format ukuran file menjadi KB atau MB.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Label Admin Level (PROGAR, PEKAS, SETUM, Super Admin)
     */
    public function getAdminLevelLabelAttribute(): string
    {
        return match ($this->admin_level) {
            'admin_1' => 'PROGAR',
            'admin_2' => 'PEKAS',
            'admin_3' => 'SETUM',
            default => 'Super Admin',
        };
    }
}
