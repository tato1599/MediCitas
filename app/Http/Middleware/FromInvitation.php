<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class FromInvitation
{
    public function handle(Request $request, Closure $next)
    {
        // Detecta si la ruta actual es de invitación
        if (
            $request->is('team-invitations/*') &&
            $request->route('invitation')
        ) {
            $invitationId = $request->route('invitation');

            $invitation = Jetstream::teamInvitationModel()::find($invitationId);

            if ($invitation) {
                session(['invited_email' => $invitation->email]);
                session(['paid' => true]); // Para permitir acceso al registro
            }
        }

        return $next($request);
    }
}
