<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PublicAppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/agendamento');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/professionals', [ProfessionalController::class, 'index'])->name('professionals');
    Route::post('/professionals', [ProfessionalController::class, 'store'])->name('professionals.store');
    Route::put('/professionals/{professional}', [ProfessionalController::class, 'update'])->name('professionals.update');
    Route::delete('/professionals/{professional}', [ProfessionalController::class, 'destroy'])->name('professionals.destroy');

    Route::get('/availabilities', [AvailabilityController::class, 'index'])->name('availabilities');
    Route::post('/availabilities',[AvailabilityController::class, 'store'])->name('availabilities.store');
    Route::put('/availabilities/{availability}',[AvailabilityController::class, 'update'])->name('availabilities.update');
    Route::delete('/availabilities/{availability}',[AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
   
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.slots');
    Route::post('/appointments',[AppointmentController::class, 'store'])->name('appointments.store');
    Route::put('/appointments/{appointment}',[AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}',[AppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}',[ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::prefix('agendamento')->name('public.')->group(function () {
    Route::get('/', [PublicAppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/store', [PublicAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/by-document', [PublicAppointmentController::class, 'byDocument'])->name('appointments.byDocument');
    Route::get('/available-slots', [PublicAppointmentController::class, 'availableSlots'])->name('appointments.slots');
});

Route::get('/cadastrar', function(){return view('register');})->name('cadastrar');
Route::post('/cadastrar', [ClientController::class, 'store'])->name('cadastrar.store');


require __DIR__.'/auth.php';
