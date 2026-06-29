<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenbutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'renbut_id',
        'item_name',
        'quantity',
        'estimated_unit_price',
        'total_price',
    ];

    public function renbut(): BelongsTo
    {
        return $this->belongsTo(Renbut::class);
    }
}
