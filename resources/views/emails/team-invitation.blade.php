@component('mail::message')
# Te han invitado a un equipo: {{ $team }}

Haz clic en el botón para unirte al equipo.

@component('mail::button', ['url' => $url])
Unirme al equipo
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
