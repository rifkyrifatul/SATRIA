<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'slug', 'review_type'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'review_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'category_id');
    }
}
