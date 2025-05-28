<?php

namespace App\Livewire\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('Actualizar Cita Médica')]
#[Layout('layouts.app')]
class AppointmentUpdate extends Component
{
    use Toast;

    public Appointment $appointment;

    public array $statuses;

    public $current_status;

    public $real_end_time;

    public function mount($appointmentId)
    {
        $this->appointment = Appointment::with(['patient', 'location', 'appointmentType', 'employee'])->findOrFail($appointmentId);
        $this->current_status = ucwords(strtolower(AppointmentStatus::fromValue($this->appointment->status)->name));
        $this->statuses = collect(AppointmentStatus::toLiveWireArray())
            ->filter(function ($status) {
                if ($this->appointment->status === $status['id']) {
                    return true;
                }

                return ! in_array($status['id'], [AppointmentStatus::REPROGRAMADO->value, AppointmentStatus::PROGRAMADO->value]);
            })->toArray();
    }

    public function updateStatus($status, $notes, $endTime = null)
    {
        try {
            Validator::make(
                [
                    'status' => $status,
                    'notes' => $notes,
                    'endTime' => $endTime,
                ],
                [
                    'status' => 'required|in:' . implode(',', array_column(AppointmentStatus::toLiveWireArray(), 'id')),
                    'notes' => 'nullable|string',
                    'endTime' => 'required_if:status,RE|date_format:H:i',
                ],
                [
                    'status.required' => 'El estado es requerido',
                    'status.in' => 'El estado seleccionado no es válido',
                    'notes.required' => 'Las notas son requeridas',
                    'notes.string' => 'Las notas deben ser un texto',
                    'endTime.required_if' => 'La hora de finalización es requerida',
                    'endTime.date_format' => 'La hora de finalización no es válida',
                ]
            )->validate();
            $this->appointment->status = $status;
            $this->appointment->notes = $notes;
            if ($status === AppointmentStatus::REALIZADO->value) {
                $this->appointment->real_end_time = $endTime;
            }
            $this->appointment->save();
            $this->toast('success', 'Estado actualizado correctamente', redirectTo: route('web.appointments.index'));
        } catch (ValidationException $e) {
            $this->toast('error', $e->getMessage(), css: 'bg-red-500');
        } catch (\Exception $e) {
            $this->toast('error', 'Ocurrió un error al actualizar el estado', css: 'bg-red-500');
        }
    }

    public function reschedule($date, $time)
    {
        try {
            Validator::make(
                [
                    'date' => $date,
                    'time' => $time,
                ],
                [
                    'date' => 'required|date_format:Y-m-d|after_or_equal:today',
                    'time' => 'required|date_format:H:i',
                ],
                [
                    'date.required' => 'La fecha es requerida',
                    'date.date_format' => 'La fecha no es válida',
                    'date.after_or_equal' => 'La fecha debe ser igual o posterior a hoy',
                    'time.required' => 'La hora es requerida',
                    'time.date_format' => 'La hora no es válida',
                ]
            )->validate();
            $date = Carbon::parse($date . ' ' . $time);
            if ($date->isPast()) {
                throw ValidationException::withMessages(['date' => 'La fecha debe ser igual o posterior a la fecha actual']);
            }
            DB::beginTransaction();
            $this->appointment->status = AppointmentStatus::REPROGRAMADO->value;
            $newAppointment = $this->appointment->replicate();
            $newAppointment->start_time = $date->format('Y-m-d H:i');
            $newAppointment->estimated_end_time = $date->addMinutes($this->appointment->duration)->format('Y-m-d H:i');
            $newAppointment->status = AppointmentStatus::PROGRAMADO->value;
            $newAppointment->save();
            $this->appointment->save();
            DB::commit();
            $this->toast('success', 'Cita reprogramada correctamente', redirectTo: route('web.appointments.index'));
        } catch (ValidationException $e) {
            DB::rollBack();
            $this->toast('error', $e->getMessage(), css: 'bg-red-500');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toast('error', 'Ocurrió un error al reprogramar la cita', css: 'bg-red-500');
        }
    }
}
