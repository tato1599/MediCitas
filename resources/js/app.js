import './bootstrap';
import { Calendar } from '@fullcalendar/core';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import rrulePlugin from '@fullcalendar/rrule';

window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.timeGridPlugin = timeGridPlugin;
window.rrulePlugin = rrulePlugin;
