<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDivision extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class, 'division_id');
    }
}
