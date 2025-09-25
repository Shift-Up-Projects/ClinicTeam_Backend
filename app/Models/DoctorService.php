<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DoctorService extends Model
{
    protected $fillable = [
      'doctor_id',
      'service_id',
    ];

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class, 'id', 'doctor_id');
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
}
