<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorTimeOff extends Model
{
    protected $fillable = [
        'doctor_id',
        'start_at',
        'end_at',
        'reason',
    ];
}
