<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.bearer')->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);

    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);
    Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);

    Route::get('/appointments/slots/available', [AppointmentController::class, 'availableSlots']);
    Route::get('/appointments/calendar/events', [AppointmentController::class, 'calendarEvents']);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::put('/appointments/{appointment}/approve', [AppointmentController::class, 'approve']);
        Route::put('/appointments/{appointment}/reject', [AppointmentController::class, 'reject']);
        Route::put('/appointments/{appointment}/complete', [AppointmentController::class, 'complete']);
    });
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Not found.',
    ], 404);
});
