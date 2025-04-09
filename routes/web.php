<?php

use App\Livewire\Patients\Create;
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
        Route::get('/index', function () {
            return view('patients.index');
        })->name('patients.index');
        Route::get('/create', function() {
            return view('patients.create');
        })->name('patients.create');
    });


});
