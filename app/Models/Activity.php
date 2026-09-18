<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['event_at' => 'datetime', 'published' => 'boolean'];
    }

    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }
}
