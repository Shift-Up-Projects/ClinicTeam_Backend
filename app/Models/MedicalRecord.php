<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MedicalRecord extends Model
{
    protected $fillable = [
        'appointment_id',
        'notes',
    ];

    public $timestamps = false;

    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class, 'id', 'appointment_id');
    }
}
