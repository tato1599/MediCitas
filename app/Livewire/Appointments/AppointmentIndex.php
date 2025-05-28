<?php

namespace App\Livewire\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Traits\AddsToast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Citas Médicas')]
#[Layout('layouts.app')]
class AppointmentIndex extends Component
{
    use AddsToast;

    public $doctorId;

    public $doctors;

    public function mount()
    {
        $this->doctorId = Auth::user()->id;
    }

    public function loadAppointments($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        // if both dates are before the current date return an empty array
        if ($start->isBefore(now()) && $end->isBefore(now())) {
            return [];
        }

        // if the start date is before the current date, set the start date to the current date
        if ($start->isBefore(now())) {
            $start = now();
        }

        $appointments = Appointment::where('start_time', '>=', $start)
            ->where('start_time', '<=', $end)
            ->where('employee_id', $this->doctorId)
            ->whereNotIn('status', [
                AppointmentStatus::REALIZADO->value,
                AppointmentStatus::CANCELADO->value,
                AppointmentStatus::AUSENTE->value,
                AppointmentStatus::REPROGRAMADO->value,
            ])
            ->with(['patient', 'appointmentType', 'employee'])
            ->get();
        $colors = AppointmentStatus::getColors();

        /**
         * @param  \App\Models\Appointment  $appointment
         */
        $events = $appointments->map(function ($appointment) use ($colors) {
            $patient = $appointment->patient;

            return [
                'title' => 'Cita con ' . $patient->full_name,
                'start' => $appointment->start_time,
                'end' => $appointment->estimated_end_time,
                'color' => $colors[$appointment->status],
                'backgroundColor' => $colors[$appointment->status],
                'extendedProps' => [
                    'id' => $appointment->id,
                    'patient' => $patient->full_name,
                    'status' => ucfirst(strtolower(AppointmentStatus::fromValue($appointment->status)->name)),
                    'duration' => $appointment->duration,
                    'type' => $appointment->appointmentType->name,
                    'doctor' => $appointment->employee->name . ' ' . $appointment->employee->first_last_name,
                    'start_time' => Carbon::parse($appointment->start_time)->format('Y-m-d H:i'),
                    'end_time' => Carbon::parse($appointment->estimated_end_time)->format('Y-m-d H:i'),
                ],
            ];
        })->toArray();

        return $events;
    }

    public function createAppointment($date)
    {
        $this->redirect(route('appointments.create', ['date' => $date, 'doctorId' => $this->doctorId]));
    }

    public function updateAppointment($appointmentId)
    {
        $this->redirect(route('appointments.update', ['appointment' => $appointmentId]));
    }
}
