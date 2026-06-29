<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailRegistry extends Model
{
    protected $fillable = [
        'type',
        'reference_number',
        'date',
        'origin_destination',
        'subject',
        'file_path',
        'uploaded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function dispositions()
    {
        return $this->hasMany(MailRegistryDisposition::class);
    }
}
