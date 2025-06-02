<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Patient;
use App\Traits\AddsToast;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('Crear Cita Médica')]
#[Layout('layouts.app')]
class AppointmentCreate extends Component
{
    use AddsToast, Toast;

    public $appointmentTypes;

    public Collection $patients;

    public ?string $date = null;

    public ?int $doctorId = null;

    public ?string $patientId = null;

    public int $appointmentTypeId;

    public string $startTime;

    public function mount(Request $request)
    {
        $date = $request->get('date');
        $doctorId = $request->get('doctorId');
        $this->search('a');
        $this->getAppointmentTypes();

        $parsedDate = Carbon::parse($date);
        $this->date = $parsedDate->format('Y-m-d');
        $this->startTime = $parsedDate->format('H:i');
        $this->doctorId = $doctorId;
    }

    public function search(string $value = '')
    {
        $selectedOption = Patient::find($this->patientId);
        $fields = ['first_name', 'last_name'];
        $patients = Patient::query()
            ->where(function ($query) use ($value, $fields) {
                foreach ($fields as $field) {
                    $query->orWhere($field, '%', $value);
                    $query->orWhere($field, 'ilike', $value . '%');
                    $query->orWhere($field, 'ilike', '%' . $value);
                }
            });
        foreach ($fields as $field) {
            $patients->orderByRaw("similarity($field, ?) DESC", [$value]);
        }
        $patients = $patients->limit(10)->get();
        $this->patients = $patients;
        if ($selectedOption) {
            $this->patients->merge($selectedOption);
        }
        $this->patients = $this->patients->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->names . ' ' . $patient->last_name,
            ];
        });
    }

    public function showToast($type, $title, $class)
    {
        $this->toast($type, $title, css: $class);
    }

    public function getAppointmentTypes()
    {
        $this->appointmentTypes = AppointmentType::all()->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'duration' => $type->duration,
            ];
        });
    }

    public function saveAppointment()
    {
        try {
            $this->validate([
                'date' => 'required|date',
                'doctorId' => 'required|integer',
                'appointmentTypeId' => 'required|integer',
                'patientId' => 'required|integer',
                'startTime' => 'required',
            ]);

            $startTime = Carbon::parse($this->date . ' ' . $this->startTime);

            $duration = $this->appointmentTypes->firstWhere('id', $this->appointmentTypeId)['duration'];

            $appointment = Appointment::create([
                'employee_id' => $this->doctorId,
                'appointment_type_id' => $this->appointmentTypeId,
                'duration' => $duration,
                'start_time' => $startTime,
                'estimated_end_time' => $startTime->copy()->addMinutes($duration),
                'confirmed_at' => Carbon::now(),
                'patient_id' => $this->patientId,
            ]);

            if (! $appointment) {
                throw new \Exception('Error al crear la cita');
            }

            // $this->toast('success', 'Cita creada correctamente', redirectTo: route('web.appointments.index'));
            $this->addToast('Éxito', 'Cita creada correctamente', 'success', true);
            $this->redirect(route('appointments.index'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $reason = $e->validator->errors()->first();
            // $this->toast('error', $reason, css: 'bg-red-500');
            $this->addToast('Error', $reason, 'error');
        } catch (\Exception $e) {
            // $this->toast('error', 'Error al crear la cita: ' . $e->getMessage(), css: 'bg-red-500');
            $this->addToast('Error', 'Error al crear la cita', 'error');
        }
    }
}
