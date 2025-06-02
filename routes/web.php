<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScheduleController;
use App\Livewire\Appointments\AppointmentCreate;
use App\Livewire\Appointments\AppointmentIndex;
use App\Livewire\Appointments\AppointmentUpdate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeCheckoutController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::get('/', function () {
    return view('welcome');
});

// ✅ Checkout de Stripe — ahora accesible sin login
Route::get('/checkout', [StripeCheckoutController::class, 'checkout'])->name('checkout');
Route::get('/checkout/success', [StripeCheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [StripeCheckoutController::class, 'cancel'])->name('checkout.cancel');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('paid.access')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('paid.access');
    Route::get('/invitacion/{invitation}', function ($invitationId) {
        $invitation = \Laravel\Jetstream\TeamInvitation::findOrFail($invitationId);

        session([
            'invited_email' => $invitation->email,
            'invited_team_id' => $invitation->team_id,
            'paid' => true,
        ]);

        return redirect('/register');
    })->name('invitation.register');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('patients')->group(function () {
        Route::get('/index', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/create', [PatientController::class, 'create'])->name('patients.create');
    });

    Route::prefix('schedules')->group(function () {
        Route::get('/index', [ScheduleController::class, 'index'])->name('schedules.index');
    });

    Route::prefix('appointments')->group(function () {
        Route::get('/', AppointmentIndex::class)->name('appointments.index');
        Route::get('/create', AppointmentCreate::class)->name('appointments.create');
        Route::get('/update/{appointment}', AppointmentUpdate::class)->name('appointments.update');
    });
});
