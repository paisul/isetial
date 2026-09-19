<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationPeriod extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'ends_at' => 'date', 'is_active' => 'boolean'];
    }

    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }

    public function assignments()
    {
        return $this->hasMany(PositionAssignment::class);
    }
}
