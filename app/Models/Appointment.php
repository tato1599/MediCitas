<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'canceled_at',
        'canceled_by',
        'reprogrammed_at',
        'reprogrammed_by',
        'no_showed_at',
        'no_showed_by',
        'confirmed_at',
        'confirmed_by',
    ];

    protected static function booted()
    {
        static::creating(function ($appointment) {
            $appointment->created_by = Auth::id();
        });

        static::updating(function ($appointment) {
            $appointment->updated_by = Auth::id();
        });
    }

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
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class);
    }
}
