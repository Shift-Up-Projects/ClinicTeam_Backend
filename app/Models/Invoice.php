<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $fillable = [
        'appointment_id',
        'total_amount',
        'status',
    ];

    public function appointment(): HasOne
    {
       return $this->hasOne(Appointment::class, 'id', 'appointment_id');
    }
}
