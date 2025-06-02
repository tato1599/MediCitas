<div class="p-6 ">
    @if (!Auth::user()->google_id)
        <div class="flex justify-end mb-4 mr-4">
            <x-button wire:click="connectGoogle"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">

                <svg class="w-5 h-5 mr-3" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                    xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                    <path fill="#EA4335"
                        d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z">
                    </path>
                    <path fill="#4285F4"
                        d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z">
                    </path>
                    <path fill="#FBBC05"
                        d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z">
                    </path>
                    <path fill="#34A853"
                        d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z">
                    </path>
                    <path fill="none" d="M0 0h48v48H0z"></path>
                </svg>

                <span class="text-black">Conectar con Google</span>
            </x-button>
        </div>
    @endif
    <div id="calendar" wire:ignore class="mt-4 mb-8 mr-4 ml-4"></div>

    @script
        <script>
            let calendarEl = document.getElementById('calendar');
            window.calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: esLocale,
                nowIndicator: true,
                showNonCurrentDates: false,
                events: async function(info, successCallback, failureCallback) {
                    res = await $wire.loadAppointments(info.startStr, info.endStr)
                    successCallback(res);
                },
                dateClick: function(info) {
                    dateRegex = /\d{4}-\d{2}-\d{2}/;
                    timeRegex = /(?<=T)(\d{2}:\d{2})/;
                    dateMatch = info.dateStr.match(dateRegex);
                    timeMatch = info.dateStr.match(timeRegex);
                    selectedDate = `<span class='text-primary'>${dateMatch[0]}</span>`;
                    if (timeMatch) {
                        selectedDate += ` a las <span class='text-primary'>${timeMatch[0]}</span>`;
                    }
                    const doctorId = $wire.doctorId;

                    dateNow = new Date();
                    if (info.date < dateNow) {
                        $wire.showToast('error', 'No puedes agendar citas en fechas pasadas', 'alert-error');
                        return;
                    }
                    Swal.fire({
                        title: 'Crear cita',
                        html: `¿Deseas agendar una cita para el ${selectedDate}?`,
                        showDenyButton: true,
                        confirmButtonText: `Sí, agendar`,
                        denyButtonText: `Cancelar`,
                        customClass: {
                            confirmButton: 'btn btn-primary focus:ring-0 focus:outline-none',
                            denyButton: 'btn btn-secondary focus:ring-0 focus:outline-none',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.createAppointment(info.dateStr);
                        }
                    });
                },
                dayCellClassNames: function(arg) {
                    if (arg.isPast) {
                        return 'hover:bg-gray-300';
                    } else {
                        return 'hover:bg-blue-200';
                    }
                },
                slotLaneClassNames: function(arg) {
                    if (arg.isPast) {
                        return 'hover:bg-gray-300';
                    } else {
                        return 'hover:bg-blue-200';
                    }
                },
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    console.log(props);
                    // html showing: status, duration, type, doctor, datetime
                    const html = `
                        <div class="flex flex-col text-sm md:text-base lg:text-lg">
                            <span class="font-semibold">
                                Estado: <span class="text-primary">${props.status}</span>
                            </span>
                            <span class="font-semibold">
                                Duración: <span class="text-primary">${props.duration}</span>
                            </span>
                            <span class="font-semibold">
                                Tipo: <span class="text-primary">${props.type}</span>
                            </span>
                            <span class="font-semibold">
                                Doctor: <span class="text-primary">${props.doctor}</span>
                            </span>
                            <span class="font-semibold">
                                Fecha Inicio: <span class="text-primary">${props.start_time}</span>
                            </span>
                            <span class="font-semibold">
                                Fecha Fin: <span class="text-primary">${props.end_time}</span>
                            </span>
                        </div>
                    `;
                    Swal.fire({
                        title: 'Cita con ' + props.patient,
                        html: html,
                        showCancelButton: true,
                        confirmButtonText: `Actualizar`,
                        cancelButtonText: `Cancelar`,
                        customClass: {
                            popup: 'max-w-xs md:max-w-md lg:max-w-lg', // Responsividad de la ventana
                            title: 'text-center', // Centra el título
                            htmlContainer: 'text-left', // Alinea el contenido a la izquierda
                            confirmButton: 'btn btn-primary focus:ring-0 focus:outline-none text-sm md:text-base', // Botón de Actualizar
                            cancelButton: 'btn btn-secondary focus:ring-0 focus:outline-none text-sm md:text-base', // Botón de Cancelar
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.updateAppointment(props.id);
                        }
                    });
                },
            });
            calendar.render();
        </script>
    @endscript

</div>
