<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['due_at' => 'date'];
    }
}
