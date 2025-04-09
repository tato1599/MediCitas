<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Livewire\Component;

class Create extends Component
{

    public $name;
    public $last_name;
    public $email;
    public $phone;
    public $dob;

    protected $rules = [
        'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'email|max:255',
        'phone' => 'string|max:15',
        'dob' => 'date',
    ];

    public function store() {
        $this->validate();

        Patient::create([
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' => $this->dob,
        ]);

        dd('Patient created successfully!');
        $this->reset(['name', 'last_name', 'email', 'phone', 'dob']);
        return redirect()->route('patients.index');
    }


}
