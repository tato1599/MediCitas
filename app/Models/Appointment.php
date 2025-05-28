<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'employee_id',
        'appointment_type_id',
        'status',
        'duration',
        'start_time',
        'estimated_end_time',
        'real_end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'id', 'employee_id');
    }

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class);
    }
}
