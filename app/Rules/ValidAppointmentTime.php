<?php

namespace App\Rules;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAppointmentTime implements ValidationRule
{
    protected $doctorServiceId;
    protected $date;
    protected $ignoreId;

    public function __construct($doctorServiceId, $date, $ignoreId = null)
    {
        $this->doctorServiceId = $doctorServiceId;
        $this->date = $date;
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startTime = $value;
        $snapshot = request()->all()['components'][0]['snapshot'];
        $data = json_decode($snapshot)->data->data[0];
        $endTime = $data->end_at;

        // Get doctor service with doctor relationship
        $doctorService = DoctorService::with('doctor')->findOrFail($this->doctorServiceId);
        $doctor = $doctorService->doctor;

        // Check if doctor is on vacation
        if ($doctor->timeOffs()
            ->whereDate('start_at', '<=', $this->date)
            ->whereDate('end_at', '>=', $this->date)
            ->exists()) {
            $fail('The doctor is on vacation on the selected date.');
            return;
        }

        // Check if the selected time is within doctor's working hours
        $dayOfWeek = strtolower(Carbon::parse($this->date)->englishDayOfWeek);
        $doctorSchedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$doctorSchedule) {
            $fail('The doctor is not available on the selected day.');
            return;
        }

        $scheduleStart = Carbon::parse($doctorSchedule->start_time);
        $scheduleEnd = Carbon::parse($doctorSchedule->end_time);
        $appointmentStart = Carbon::parse($startTime);
        $appointmentEnd = Carbon::parse($endTime);

        if ($appointmentStart->lt($scheduleStart) || $appointmentEnd->gt($scheduleEnd)) {
            $fail('The appointment time must be within the doctor\'s working hours (' .
                  $scheduleStart->format('g:i A') . ' - ' . $scheduleEnd->format('g:i A') . ').');
            return;
        }

        // Check for overlapping appointments
        $startTime = Carbon::parse($startTime)->format('H:i:s');
        $endTime = Carbon::parse($endTime)->format('H:i:s');
        $overlappingAppointment = Appointment::where('doctor_service_id', $this->doctorServiceId)
            ->where('date', $this->date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    // New appointment starts during an existing appointment
                    $q->where('start_at', '<=', $startTime)
                      ->where('end_at', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New appointment ends during an existing appointment
                    $q->where('start_at', '<', $endTime)
                      ->where('end_at', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New appointment completely contains an existing appointment
                    $q->where('start_at', '>', $startTime)
                      ->where('end_at', '<', $endTime);
                });
            });

        if ($this->ignoreId) {
            $overlappingAppointment->where('id', '!=', $this->ignoreId);
        }

        if ($overlappingAppointment->exists()) {
            $fail('There is already an appointment scheduled for this doctor during the selected time.');
        }
    }
}
