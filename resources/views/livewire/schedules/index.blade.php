<div class="p-6 " x-data="schedules">
    <style>
        .bg-meal {
            background-color: var(--color-meal);
        }

        .bg-work-exception {
            background-color: var(--color-work-exception);
        }

        .border-none {
            border: none;
        }
    </style>

    <div class="grid grid-cols-2 gap-4">
        <h1 class="text-2xl font-bold">Horario</h1>
        <div class="flex justify-end w-full">
            <button class="bg-basic lg:w-1/4 w-1/2 md:w-1/3 mt-9 py-2 px-4 text-white rounded-lg cursor-pointer" @click="save">
                <template x-if="saving">
                    <center>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2">
                                <path stroke-dasharray="16" stroke-dashoffset="16" d="M12 3c4.97 0 9 4.03 9 9">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.3s"
                                        values="16;0" />
                                    <animateTransform attributeName="transform" dur="1.5s" repeatCount="indefinite"
                                        type="rotate" values="0 12 12;360 12 12" />
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
                    <span>Guardar</span>
                </template>
            </button>
        </div>
    </div>

    <div class="flex gap-4 mt-4">
        <div class="shadow-lg rounded-lg p-2 w-3/4">
            <div class="flex justify-between">
                <p><b>Horas de trabajo</b></p>
                <button class="bg-basic text-white rounded-lg px-2 max-h-6 min-h-6 cursor-pointer" @click="createSchedule">+</button>
            </div>
            <div id="work-hours" class="flex overflow-x-auto gap-2" x-ref="workHours">
                @foreach ($schedules as $schedule)
                    <div @if ($schedule['title'] != 'Custom') style="background-color: {{ $schedule['color'] }}; color: {{ $schedule['textColor'] }};" @endif
                        @class([
                            'bg-work-exception' => $schedule['title'] == 'Custom',
                            'hover:bg-work-exception' => $schedule['title'] == 'Custom',
                            'border-none',
                            'text-secondary-content',
                            'rounded-lg',
                            'w-fit',
                            'btn',
                            'mt-2',
                            'work-hour',
                            'py-2',
                            'px-4',
                            'text-white',
                            'cursor-pointer'
                        ]) data-event="{{ $schedule['data_event'] }}">
                        {{ $schedule['title'] }}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="shadow-lg rounded-lg p-2 w-1/4">
            <p><b>Hora de comida</b></p>
            <div id="meal" class="bg-meal hover:bg-meal-hover text-secondary-content rounded-lg w-fit btn mt-2 py-2 px-4 text-white cursor-pointer">
                Comida
            </div>
        </div>
    </div>

    <div id="calendar" wire:ignore class="mt-4"></div>

    @script
        <script>
            let calendarEl = document.getElementById('calendar');
            let mealDraggableEl = document.getElementById('meal');
            let workHourDraggableEl = document.getElementById('work-hours');

            let mealDragable = new Draggable(mealDraggableEl, {
                eventData: {
                    title: 'Comida',
                    extendedProps: {
                        meal: true,
                        new: true,
                    },
                    startEditable: true,
                    className: ['bg-meal', 'border-none'],
                }
            });

            let workHourDragable = new Draggable(workHourDraggableEl, {
                itemSelector: '.work-hour',
            });

            function overlaps(event, events) {
                for (let e of events) {
                    if (e.extendedProps.meal || e.display == "background") continue;
                    if (event.start >= e.start && event.start < e.end &&
                        event.end > e.start && event.end <= e.end) {
                        return true;
                    }
                }
                return false;
            }

            window.calendar = new Calendar(calendarEl, {
                plugins: [timeGridPlugin, interactionPlugin],
                initialView: 'timeGridWeek',
                locale: esLocale,
                contentHeight: 620,
                droppable: true,
                editable: true,
                validRange: {
                    start: $wire.calendarStart,
                    end: $wire.calendarEnd,
                },
                events: async function(info, successCallback, failureCallback) {
                    res = await $wire.loadEvents(info.startStr, info.endStr);
                    successCallback(res);
                },
                eventReceive: async function(info) {
                    if (info.event.extendedProps.meal) {
                        end = new Date(info.event.start);
                        end.setTime(end.getTime() + 1 * 60 * 60 * 1000);
                        info.event.setEnd(end);
                        if (!overlaps(info.event, calendar.getEvents())) {
                            info.revert();
                            return;
                        }
                        let id = await $wire.adduserSchedule(info.event, "userMeals");
                        window.calendar.refetchEvents();
                        info.revert();
                        return;
                    } else if (info.event.extendedProps.exception) {
                        let id = await $wire.adduserSchedule(info.event, "userExceptions");
                        window.calendar.refetchEvents();
                        info.revert();
                        return;
                    }
                    let [hour, minute] = info.event.extendedProps.start.split(':').map(Number);
                    let start = new Date(info.event.start).setHours(hour, minute);
                    info.event.setStart(start, {
                        maintainDuration: true
                    });
                    let id = await $wire.adduserSchedule(info.event, "userSchedules");
                    window.calendar.refetchEvents();
                    info.revert();
                },
                eventDrop: async function(info) {
                    if (info.event.extendedProps.meal) {
                        if (!overlaps(info.event, calendar.getEvents())) {
                            info.revert();
                            return;
                        }
                        $wire.updateuserSchedule(info.oldEvent, info.event, "userMeals");
                        return;
                    } else if (info.event.extendedProps.exception) {
                        await $wire.updateuserSchedule(info.oldEvent, info.event, "userExceptions");
                        window.calendar.refetchEvents();
                        return;
                    }
                    let [hour, minute] = info.event.extendedProps.start.split(':').map(Number);
                    let start = new Date(info.event.start).setHours(hour, minute);
                    info.event.setStart(start, {
                        maintainDuration: true
                    });
                    await $wire.updateuserSchedule(info.oldEvent, info.event, "userSchedules");
                    window.calendar.refetchEvents();
                },
                eventRemove: async function(info) {
                    if (info.event.extendedProps.meal) {
                        $wire.removeuserSchedule(info.event, "userMeals");
                        return;
                    } else if (info.event.extendedProps.exception) {
                        await $wire.removeuserSchedule(info.event, "userExceptions");
                        window.calendar.refetchEvents();
                        return;
                    }
                    await $wire.removeuserSchedule(info.event, "userSchedules");
                    window.calendar.refetchEvents();
                },
                eventOverlap: function(stillEvent, movingEvent) {
                    return (stillEvent.extendedProps.meal ^ movingEvent.extendedProps.meal) ||
                        (stillEvent.extendedProps.exception ^ movingEvent.extendedProps.exception);
                },
                eventClick: function(info) {
                    if (info.event.display == "background") return;
                    Swal.fire({
                        'title': 'Eliminar horario',
                        'text': '¿Estás seguro de eliminar este horario?',
                        'icon': 'warning',
                        'showCancelButton': true,
                        'confirmButtonText': 'Eliminar',
                        'cancelButtonText': 'Cancelar',
                        'customClass': {
                            'popup': 'rounded-lg shadow-lg p-6', // Mejora el estilo del cuadro de diálogo
                            'confirmButton': 'btn btn-primary focus:ring-0 focus:outline-none', // Estilo del botón de confirmar
                            'cancelButton': 'btn btn-secondary focus:ring-0 focus:outline-none' // Estilo del botón de cancelar
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            info.event.remove();
                        }
                    });
                },
                eventDragStop: function(info) {
                    let [x, y] = [info.jsEvent.clientX, info.jsEvent.clientY];
                    let rect = info.view.calendar.el.getBoundingClientRect();
                    if (x < rect.left || x >= rect.right || y < rect.top || y >= rect.bottom) {
                        info.event.remove();
                    }
                },
                eventResize: async function(info) {
                    await $wire.resizeuserException(info.event);
                    window.calendar.refetchEvents();
                },
                allDaySlot: false,
            });
            calendar.render();

            Alpine.data('schedules', () => ({
                changed: $wire.entangle('changed'),
                saving: false,
                async updateEvents() {
                    if (this.changed) {
                        await Swal.fire({
                            title: 'Cambios sin guardar',
                            text: '¿Deseas guardar los cambios antes de continuar?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Descartar',
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'rounded-lg shadow-lg p-6',
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary'
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.save();
                            }
                        });
                    }
                    window.calendar.refetchEvents();
                },
                async createSchedule() {
                    await Swal.fire({
                        title: 'Crear horario',
                        html: `
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="schedule_name" class="label label-text font-semibold mb-2">Nombre</label>
                                    <input type="text" id="schedule_name" class="input input-bordered w-full" placeholder="Ej. Turno Matutino">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="start" class="label label-text font-semibold mb-2">Inicio</label>
                                        <input type="time" id="start" class="input input-bordered w-full">
                                    </div>
                                    <div>
                                        <label for="hours" class="label label-text font-semibold mb-2">Duración</label>
                                        <div id="custom-time-input" style="display: flex; align-items: center; gap: 5px; font-size: 16px;">
                                            <input type="number" id="hours" placeholder="HH" min="0" max="72" class="input input-bordered"/>
                                            <span>:</span>
                                            <input type="number" id="minutes" placeholder="MM" min="0" max="59"
                                            class="input input-bordered" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label for="color" class="label label-text font-semibold mb-2">Color</label>
                                    <input type="color" id="color" class="input input-bordered w-full" value="#09A7ED">
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            popup: 'rounded-lg shadow-lg p-6', // Mejora el estilo del cuadro de diálogo
                            confirmButton: 'btn btn-primary', // Estilo del botón de confirmar
                            cancelButton: 'btn btn-secondary' // Estilo del botón de cancelar
                        },
                        showLoaderOnConfirm: true,
                        didOpen: () => {
                            // Validación en tiempo real para los campos de duración
                            const hoursInput = document.getElementById('hours');
                            const minutesInput = document.getElementById('minutes');

                            hoursInput.addEventListener('input', () => {
                                if (hoursInput.value > 72) hoursInput.value = 72;
                                if (hoursInput.value < 0) hoursInput.value = 0;
                            });

                            minutesInput.addEventListener('input', () => {
                                if (minutesInput.value >= 60) {
                                    minutesInput.value = 59; // Ajustar minutos máximos
                                }
                                if (minutesInput.value < 0) {
                                    minutesInput.value = 0;
                                }
                            });
                        },
                        preConfirm: async () => {
                            // Obtener valores de los campos
                            const name = document.getElementById('schedule_name').value.trim();
                            const start = document.getElementById('start').value;
                            const durationHours = document.getElementById('hours').value;
                            const durationMinutes = document.getElementById('minutes').value;

                            // Validar campos
                            if (!name) {
                                Swal.showValidationMessage(
                                    'El campo "Nombre" es obligatorio');
                                return false;
                            }

                            if (!start) {
                                Swal.showValidationMessage(
                                    'El campo "Inicio" es obligatorio');
                                return false;
                            }

                            if (!durationHours || !durationMinutes) {
                                Swal.showValidationMessage(
                                    'Los campos de duración son obligatorios');
                                return false;
                            }
                            if (durationHours == 0 && durationMinutes == 0) {
                                Swal.showValidationMessage(
                                    'La duración no puede ser 0');
                                return false;
                            }
                            const duration =
                                `${durationHours.toString().padStart(2, '0')}:${durationMinutes.toString().padStart(2, '0')}`;
                            const color = document.getElementById('color').value;

                            await $wire.createSchedule(name, start, duration, color);
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    });
                },
                async save() {
                    if (this.saving) return;
                    this.saving = true;
                    await $wire.save();
                    this.saving = false;
                    this.changed = false;
                }
            }));
        </script>
    @endscript
</div>
