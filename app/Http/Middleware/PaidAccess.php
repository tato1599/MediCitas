<?php

// app/Http/Middleware/PaidAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PaidAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('paid') && !session()->has('invited_team_id')) {
            return redirect('/')
                ->with('error', 'Debes completar el pago o haber sido invitado para registrarte.');
        }

        return $next($request);
    }
}
