<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JerseyPayment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'verified_at' => 'datetime'];
    }

    public function order()
    {
        return $this->belongsTo(JerseyOrder::class, 'jersey_order_id');
    }
}
