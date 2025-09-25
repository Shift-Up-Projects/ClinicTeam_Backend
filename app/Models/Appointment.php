<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DoctorService;
use App\Models\MedicalRecord;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_service_id',
        'date',
        'start_at',
        'end_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctorService(): BelongsTo
    {
        return $this->belongsTo(DoctorService::class, 'doctor_service_id');
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
