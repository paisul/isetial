<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Masjid extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}
