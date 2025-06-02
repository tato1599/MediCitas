<div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Actualizar Cita</h1>
    <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200">
        <div class="grid gap-4">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-semibold text-gray-600">Paciente:</span>
                <p class="text-gray-800 font-medium">
                    {{ $appointment->patient->first_name . ' ' . $appointment->patient->first_name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-semibold text-gray-600">Tipo de cita:</span>
                <p class="text-gray-800 font-medium">{{ $appointment->appointmentType->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-semibold text-gray-600">Doctor:</span>
                <p class="text-gray-800 font-medium">
                    {{ $appointment->employee->name . ' ' . $appointment->employee->first_last_name }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-semibold text-gray-600">Estado:</span>
                <p class="text-gray-800 font-medium">{{ $current_status }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-semibold text-gray-600">Fecha:</span>
                <p class="text-gray-800 font-medium">{{ $appointment->start_time }}</p>
            </div>
        </div>
    </div>
    <div class="mt-6" x-data='appointmentUpdate'>
        <div class="flex justify-between border-b border-gray-200">
            <nav class="flex space-x-4">
                <button @click="activeTab = 'status'"
                    :class="{ 'border-primary text-primary': activeTab === 'status', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'status' }"
                    class="px-3 py-2 font-medium text-sm border-b-2 focus:outline-none">
                    Actualizar Estado
                </button>
                <button @click="activeTab = 'reschedule'"
                    :class="{ 'border-primary text-primary': activeTab === 'reschedule', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'reschedule' }"
                    class="px-3 py-2 font-medium text-sm border-b-2 focus:outline-none">
                    Reagendar Cita
                </button>
            </nav>
        </div>
        <div class="mt-6 px-6 lg:w-1/2">
            <div x-show="activeTab === 'status'">
                <div>
                    <label for="status-select" class="pt-0 label label-text font-semibold mt-4">Estatus</label>
                    <x-mary-select id="status-select" :options="$statuses" x-model="status" />
                </div>
                <div x-show="status === 'RE'">
                    <label for="time-picker" class="pt-0 label label-text font-semibold mt-4">Hora de salida</label>
                    <x-mary-datetime id="time-picker" x-model="time" icon="o-clock" type="time" />
                </div>
                <div class="mt-4">
                    <label for="notes" class="pt-0 label label-text font-semibold mt-4">Notas</label>
                    <textarea x-model="notes" id="notes" name="notes" rows="3"
                        class="mt-1 block w-full shadow-sm focus:ring-primary focus:border-primary sm:text-sm border-primary rounded-md"></textarea>
                </div>
            </div>

            <div x-show="activeTab === 'reschedule'">
                <div>
                    <label for="date-picker" class="pt-0 label label-text font-semibold mt-4">Fecha</label>
                    <x-mary-datetime id="date-picker" x-model="date" icon="o-calendar" />
                </div>
                <div>
                    <label for="time-picker" class="pt-0 label label-text font-semibold mt-4">Hora</label>
                    <x-mary-datetime id="time-picker" x-model="time" icon="o-clock" type="time" />
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button @click="save" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                    <template x-if="saving">
                        <center>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2">
                                    <path stroke-dasharray="16" stroke-dashoffset="16" d="M12 3c4.97 0 9 4.03 9 9">
                                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.3s"
                                            values="16;0" />
                                        <animateTransform attributeName="transform" dur="1.5s"
                                            repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12" />
                                    </path>
                                    <path stroke-dasharray="64" stroke-dashoffset="64" stroke-opacity="0.3"
                                        d="M12 3c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9c0 -4.97 4.03 -9 9 -9Z">
                                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="1.2s"
                                            values="64;0" />
                                    </path>
                                </g>
                            </svg>
                        </center>
                    </template>
                    <template x-if="!saving">
                        <span x-text="activeTab === 'status' ? 'Actualizar Estado' : 'Reagendar Cita'">
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    @script
        <script>
            Alpine.data('appointmentUpdate', () => ({
                activeTab: 'status',
                time: '',
                date: '',
                notes: '',
                status: @json($appointment).status,
                saving: false,
                async save() {
                    if (this.saving) return;
                    this.saving = true;
                    if (this.activeTab === 'status') {
                        await $wire.updateStatus(this.status, this.notes, this.time);
                    } else {
                        await $wire.reschedule(this.date, this.time);
                    }
                    this.saving = false;
                }
            }));
        </script>
    @endscript
</div>
