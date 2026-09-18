<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['joined_at' => 'date', 'is_active' => 'boolean'];
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function homeMasjid()
    {
        return $this->belongsTo(Masjid::class, 'home_masjid_id');
    }
}
