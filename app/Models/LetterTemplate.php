<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class LetterTemplate extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_extension',
        'uploaded_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'file_extension'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    protected static function booted(): void
    {
        static::forceDeleted(function ($template) {
            if ($template->file_path && \Illuminate\Support\Facades\Storage::disk('private')->exists($template->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('private')->delete($template->file_path);
            }
        });
    }
}
