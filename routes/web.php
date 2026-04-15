<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::post('/inquiry', [HomeController::class, 'inquiryStore'])->name('inquiry.store');

Route::get('/api/services', [HomeController::class, 'apiServices']);
Route::get('/api/appointments/slots', [HomeController::class, 'getAvailableSlots']);
Route::get('/api/appointments/calendar', [HomeController::class, 'calendarEvents']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/history', [AppointmentController::class, 'history'])->name('appointments.history');
    Route::get('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::put('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule.update');
    Route::put('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/api/appointments/by-date', [AppointmentController::class, 'getAppointmentsByDate'])->name('appointments.by-date');
    
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{pet}/records', [PetController::class, 'records'])->name('pets.records');
    
    Route::get('/reminders', [DashboardController::class, 'reminders'])->name('reminders');
    Route::delete('/reminders/{reminder}', [DashboardController::class, 'deleteReminder'])->name('reminders.delete');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
    
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical-records');
    Route::post('/medical-records/{medicalRecord}/seen', [MedicalRecordController::class, 'markAsSeen'])->name('medical-records.seen');
    
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');
    Route::post('/feedback/{feedback}/reply', [FeedbackController::class, 'reply'])->name('feedback.reply');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AdminController::class, 'showAppointment'])->name('appointments.show');
    Route::put('/appointments/{appointment}/approve', [AdminController::class, 'approveAppointment'])->name('appointments.approve');
    Route::put('/appointments/{appointment}/reject', [AdminController::class, 'rejectAppointment'])->name('appointments.reject');
    Route::put('/appointments/{appointment}/complete', [AdminController::class, 'completeAppointment'])->name('appointments.complete');
    Route::delete('/appointments/{appointment}', [AdminController::class, 'destroyAppointment'])->name('appointments.destroy');
    
    Route::get('/patients', [AdminController::class, 'patients'])->name('patients.index');
    Route::get('/patients/{pet}/records', [AdminController::class, 'patientRecords'])->name('patients.records');
    Route::post('/patients/{pet}/records', [AdminController::class, 'storeMedicalRecord'])->name('patients.records.store');
    Route::delete('/records/{record}', [AdminController::class, 'deleteMedicalRecord'])->name('records.delete');
    
    Route::get('/inquiries', [AdminController::class, 'inquiries'])->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [AdminController::class, 'showInquiry'])->name('inquiries.show');
    Route::put('/inquiries/{inquiry}/status', [AdminController::class, 'updateInquiryStatus'])->name('inquiries.status');
    Route::delete('/inquiries/{inquiry}', [AdminController::class, 'deleteInquiry'])->name('inquiries.delete');
    
    Route::get('/services', [AdminController::class, 'services'])->name('services.index');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('services.delete');
    
    Route::post('/appointments/{appointment}/reminder', [AdminController::class, 'sendReminder'])->name('appointments.reminder');
    Route::delete('/reminders/{reminder}', [AdminController::class, 'deleteReminder'])->name('reminders.delete');
    
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users/admin', [AdminController::class, 'createAdmin'])->name('users.create-admin');
    Route::get('/users/{user}/pets', [AdminController::class, 'userPets'])->name('users.pets');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::delete('/pets/{pet}', [AdminController::class, 'deletePet'])->name('pets.delete');
    
    Route::get('/feedback', [AdminController::class, 'feedback'])->name('feedback.index');
    Route::put('/feedback/{feedback}', [AdminController::class, 'updateFeedback'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [AdminController::class, 'deleteFeedback'])->name('feedback.destroy');
    Route::post('/feedback/{feedback}/reply', [AdminController::class, 'replyFeedback'])->name('feedback.reply');
});

require __DIR__.'/auth.php';
