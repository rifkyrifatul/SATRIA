<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterAttachment extends Model
{
    protected $fillable = [
        'letter_id',
        'user_id',
        'file_path',
        'file_type',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
