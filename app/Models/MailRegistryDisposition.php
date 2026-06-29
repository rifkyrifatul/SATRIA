<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailRegistryDisposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_registry_id',
        'user_id',
        'note',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function mailRegistry(): BelongsTo
    {
        return $this->belongsTo(MailRegistry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
