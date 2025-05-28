<div class="p-6">
    <h1 class="text-xl font-bold mb-4">
        Crear Cita
    </h1>

    <!-- Container actualizado -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Card 1 -->
        <div class="card1">
            <div>
                <label for="appointment-choices" class="pt-0 label label-text font-semibold mt-4">Tipo de cita</label>
                <x-mary-choices-offline id="appointment-choices" wire:model="appointmentTypeId" :options="$appointmentTypes"
                    placeholder="Elige un tipo de cita..." single searchable />
            </div>
            <div>
                <label for="date-picker" class="pt-0 label label-text font-semibold mt-4">Fecha</label>
                <x-mary-datetime id="date-picker" wire:model="date" icon="o-calendar" />
            </div>
        </div>

        <!-- Card 2 -->
        <div class="card2">
            <div>
                <label for="pacient-choices" class="pt-0 label label-text font-semibold mt-4">Paciente</label>
                <x-mary-choices id="doctor-choices" wire:model="patientId" :options="$patients" values-as-string
                    no-result-text="No se encontraron pacientes" placeholder="Elige un paciente..." single searchable />
            </div>
            <div>
                <label for="time-picker" class="pt-0 label label-text font-semibold mt-4">Hora</label>
                <x-mary-datetime id="time-picker" wire:model="startTime" icon="o-clock" type="time" />
            </div>
        </div>
        <div class="flex items-right col-span-2">
            <button wire:click="saveAppointment" class="btn btn-primary mt-4 ml-auto">
                Guardar
            </button>
        </div>
    </div>

</div>
