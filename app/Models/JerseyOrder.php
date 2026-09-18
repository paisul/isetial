<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JerseyOrder extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'total' => 'decimal:2'];
    }

    public function items()
    {
        return $this->hasMany(JerseyOrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(JerseyPayment::class);
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments->where('status', 'verified')->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function getPaymentStatusAttribute()
    {
        return $this->paid_amount <= 0 ? 'Belum Bayar' : ($this->balance > 0 ? 'Cicilan' : 'Lunas');
    }
}
