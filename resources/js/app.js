import './bootstrap';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';
import Swal from 'sweetalert2';


window.Swal = Swal
window.Calendar = Calendar
window.Draggable = Draggable
window.dayGridPlugin = dayGridPlugin
window.timeGridPlugin = timeGridPlugin
window.interactionPlugin = interactionPlugin
window.esLocale = esLocale

window.Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    iconColor: 'white',
    customClass: {
        popup: 'colored-toast',
    },
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
})
