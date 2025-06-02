<?php

namespace App\Livewire\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Services\CalendarEventService;
use App\Traits\AddsToast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    use Toast, AddsToast;

    public Appointment $appointment;

    public array $statuses;

    public $current_status;

    public $real_end_time;

    protected CalendarEventService $calendarEventService;

    public function boot()
    {
        $user = Auth::user();
        if (!empty($user->google_id)) {
            $this->calendarEventService = new CalendarEventService();
        }
    }

    public function mount($appointment)
    {
        $appointment = Appointment::with(['patient', 'appointmentType', 'employee'])
            ->find($appointment)
            ->first();
        $this->appointment = $appointment;
        $this->current_status = ucwords(strtolower(AppointmentStatus::fromValue($this->appointment->status)->name));
        $this->statuses = collect(AppointmentStatus::toLiveWireArray(usingValueAsName: false))
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
                // dd($endTime, $this->appointment->start_time);
                $endTime = preg_replace("/\d{2}:\d{2}(\d{2})?/", $endTime, $this->appointment->start_time);
                $startTime = Carbon::parse($this->appointment->start_time);
                $endTime = Carbon::parse($endTime);
                if ($endTime->isBefore($startTime)) {
                    throw ValidationException::withMessages(['endTime' => 'La hora de finalización debe ser posterior a la hora de inicio']);
                }
                $this->appointment->real_end_time = Carbon::parse($this->appointment->start_time);
            }
            $this->appointment->save();
            $user = Auth::user();
            if (!empty($user->google_id) && !empty($this->appointment->event_id)) {
                if (in_array($status, [AppointmentStatus::REALIZADO->value, AppointmentStatus::AUSENTE, AppointmentStatus::CANCELADO->value])) {
                    $this->calendarEventService->deleteEvent($this->appointment->event_id);
                }
            }
            // $this->toast('success', 'Estado actualizado correctamente', redirectTo: route('web.appointments.index'));
            $this->addToast('Éxito', 'Estado actualizado correctamente', 'success', true);
            $this->redirect(route('appointments.index'));
        } catch (ValidationException $e) {
            // $this->toast('error', $e->getMessage(), css: 'bg-red-500');
            $this->addToast('Error: ', $e->getMessage(), 'error');
        } catch (\Exception $e) {
            // $this->toast('error', 'Ocurrió un error al actualizar el estado', css: 'bg-red-500');
            $this->addToast('Error', 'Ocurrió un error al actualizar el estado', 'error');
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

            $user = Auth::user();
            if (!empty($user->google_id)) {
                $eventData = [
                    'summary' => $newAppointment->appointmentType->name . ' con ' . $newAppointment->patient->first_name . ' ' . $newAppointment->patient->last_name,
                    'description' => 'Cita médica con el doctor: ' . $newAppointment->employee->name,
                    'start' => [
                        'dateTime' => $date->toIso8601String(),
                        'timeZone' => 'America/Denver',
                    ],
                    'end' => [
                        'dateTime' => $date->addMinutes($newAppointment->duration)->toIso8601String(),
                        'timeZone' => 'America/Denver',
                    ],
                ];
                $event = $this->calendarEventService->createEvent($eventData);
                if (!$event) {
                    throw new \Exception('Error al crear el evento en Google Calendar');
                }
                $newAppointment->event_id = $event->id;
                $newAppointment->save();
                if (!empty($this->appointment->event_id)) {
                    $this->calendarEventService->deleteEvent($this->appointment->event_id);
                }
            }
            DB::commit();

            $this->addToast('Éxito', 'Cita reprogramada correctamente', 'success', true);
            $this->redirect(route('appointments.index'));
        } catch (ValidationException $e) {
            DB::rollBack();
            $this->addToast('Error', $e->getMessage(), 'error');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addToast('Error', 'Ocurrió un error al reagendar la cita', 'error');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addToast('Error', 'Ocurrió un error inesperado', 'error');
        }
    }
}
