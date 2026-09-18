<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['published' => 'boolean', 'published_at' => 'datetime'];
    }

    public function masjid()
    {
        return $this->belongsTo(Masjid::class);
    }
}
