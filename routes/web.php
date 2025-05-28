<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScheduleController;
use App\Livewire\Appointments\AppointmentCreate;
use App\Livewire\Appointments\AppointmentIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
        // Route::get('/create', [PatientController::class, 'create'])->name('schedules.create');
    });

    Route::prefix('appointments')->group(function () {
        Route::get('/', AppointmentIndex::class)->name('appointments.index');
        Route::get('/create', AppointmentCreate::class)->name('appointments.create');
    });
});
