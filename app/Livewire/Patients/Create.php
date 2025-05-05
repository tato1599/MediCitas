<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use App\Traits\AddsToast;
use Livewire\Component;


class Create extends Component
{
    use AddsToast;

    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $dob;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'email|max:255|nullable|unique:patients,email',
        'phone' => 'string|max:15|nullable',
        'dob' => 'date|nullable',
    ];

    public function store() {
        $this->validate();

        Patient::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'dob' => $this->dob ?? null,
        ]);

        $this->addToast('Paciente creado', 'El paciente fue guardado exitosamente', 'success', true);
        $this->reset(['first_name', 'last_name', 'email', 'phone', 'dob']);
        return redirect()->route('patients.index');
    }


}
