<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function positions()
    {
        return $this->hasMany(PositionAssignment::class);
    }
}
