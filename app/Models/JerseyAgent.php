<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JerseyAgent extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function orders()
    {
        return $this->hasMany(JerseyOrder::class);
    }
}
