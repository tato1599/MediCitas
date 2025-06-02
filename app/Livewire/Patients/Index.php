<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $patients = Patient::where('team_id', auth()->user()->currentTeam->id)
            ->latest()
            ->get();

        return view('livewire.patients.index', compact('patients'));
    }
}
