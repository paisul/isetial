<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionAssignment extends Model
{
    protected $guarded = [];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function period()
    {
        return $this->belongsTo(OrganizationPeriod::class, 'organization_period_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
