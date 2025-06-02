<div class="p-6 ">
    <x-button wire:click="connectGoogle">
        Conectar con Google
    </x-button>

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
