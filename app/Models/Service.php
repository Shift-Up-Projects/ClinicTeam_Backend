<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Doctor;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'estimated_duration',
        'estimated_price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
    ];

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_services')->withTimestamps();
    }

    public function appointments()
    {
        return $this->hasManyThrough(
            Appointment::class,
            DoctorService::class,
            'service_id', // Foreign key on the DoctorService table
            'doctor_service_id', // Foreign key on the Appointment table
            'id', // Local key on the Service table
            'id' // Local key on the DoctorService table
        );
    }
}
