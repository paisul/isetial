<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JerseyOrder extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'total' => 'decimal:2', 'payment_code' => 'integer'];
    }

    public function items()
    {
        return $this->hasMany(JerseyOrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(JerseyPayment::class);
    }

    public function agent()
    {
        return $this->belongsTo(JerseyAgent::class, 'jersey_agent_id');
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments->where('status', 'verified')->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return max(0, $this->payable_total - (float) $this->paid_amount);
    }

    public function getPayableTotalAttribute(): float
    {
        return (float) $this->total + (($this->payment_code ?? 0) / 100);
    }

    public function getPaymentStatusAttribute()
    {
        return $this->paid_amount <= 0 ? 'Belum Bayar' : ($this->balance > 0 ? 'Cicilan' : 'Lunas');
    }
}
