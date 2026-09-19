<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $guarded = [];

    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }

    public function assignments()
    {
        return $this->hasMany(PositionAssignment::class);
    }
}
