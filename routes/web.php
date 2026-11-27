<?php

use Illuminate\Support\Facades\Route;

// Public Patient Website Routes
Route::get('/', function () {
    return view('patient.home');
})->name('home');

Route::get('/about', function () {
    return view('patient.about');
})->name('about');

Route::get('/how-it-works', function () {
    return view('patient.how-it-works');
})->name('how-it-works');

Route::get('/services', function () {
    return view('patient.services');
})->name('services');

Route::get('/faq', function () {
    return view('patient.faq');
})->name('faq');

Route::get('/contact', function () {
    return view('patient.contact');
})->name('contact');

Route::get('/privacy-policy', function () {
    return view('patient.privacy-policy');
})->name('privacy.policy');

Route::get('/cookie-policy', function () {
    return view('patient.cookie-policy');
})->name('cookie.policy');

Route::get('/terms-conditions', function () {
    return view('patient.terms-conditions');
})->name('terms.conditions');

Route::get('/accessibility', function () {
    return view('patient.accessibility');
})->name('accessibility');

// Booking Routes
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/new', [\App\Http\Controllers\Patient\BookingController::class, 'create'])->name('create');
    Route::post('/step/{step}', [\App\Http\Controllers\Patient\BookingController::class, 'storeStep'])->name('step.store');
    Route::get('/step/{step}', [\App\Http\Controllers\Patient\BookingController::class, 'showStep'])->name('step.show');
    Route::post('/submit', [\App\Http\Controllers\Patient\BookingController::class, 'submit'])
        ->middleware('throttle:10,1')
        ->name('submit');
    Route::get('/confirmed/{uuid}', [\App\Http\Controllers\Patient\BookingController::class, 'confirmed'])->name('confirmed');
    Route::get('/status/{uuid}', [\App\Http\Controllers\Patient\BookingController::class, 'status'])->name('status');
    Route::post('/status/{uuid}/cancel', [\App\Http\Controllers\Patient\BookingController::class, 'cancel'])->name('cancel');
    Route::post('/status/{uuid}/reschedule', [\App\Http\Controllers\Patient\BookingController::class, 'reschedule'])->name('reschedule');
});

// Notifications (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
});

// Operations Portal Routes
Route::prefix('operations')->name('operations.')->middleware(['auth', 'verified', 'role:super_admin,org_admin,dispatch_manager,finance_officer,compliance_officer,read_only_auditor'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Operations\DashboardController::class, 'index'])->name('dashboard');
    
    // Bookings
    Route::resource('bookings', \App\Http\Controllers\Operations\BookingController::class)->except(['create', 'store']);
    Route::post('bookings/{booking}/accept', [\App\Http\Controllers\Operations\BookingController::class, 'accept'])->name('bookings.accept');
    Route::post('bookings/{booking}/reject', [\App\Http\Controllers\Operations\BookingController::class, 'reject'])->name('bookings.reject');
    Route::post('bookings/{booking}/assign-vehicle', [\App\Http\Controllers\Operations\BookingController::class, 'assignVehicle'])->name('bookings.assign-vehicle');
    Route::post('bookings/{booking}/assign-driver', [\App\Http\Controllers\Operations\BookingController::class, 'assignDriver'])->name('bookings.assign-driver');
    Route::post('bookings/{booking}/dispatch', [\App\Http\Controllers\Operations\BookingController::class, 'dispatch'])->name('bookings.dispatch');
    Route::post('bookings/{booking}/cancel', [\App\Http\Controllers\Operations\BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('bookings/{booking}/run-sheet', [\App\Http\Controllers\Operations\BookingController::class, 'runSheet'])->name('bookings.run-sheet');
    
    // Dispatch Board
    Route::get('/dispatch', [\App\Http\Controllers\Operations\DispatchController::class, 'index'])->name('dispatch');
    Route::post('/dispatch/assign', [\App\Http\Controllers\Operations\DispatchController::class, 'assign'])->name('dispatch.assign');
    Route::post('/dispatch/release', [\App\Http\Controllers\Operations\DispatchController::class, 'release'])->name('dispatch.release');
    
    // Fleet
    Route::resource('fleet', \App\Http\Controllers\Operations\VehicleController::class)->parameters(['fleet' => 'vehicle']);
    Route::post('fleet/{vehicle}/documents', [\App\Http\Controllers\Operations\VehicleController::class, 'uploadDocument'])->name('fleet.documents.upload');
    Route::delete('fleet/{vehicle}/documents/{document}', [\App\Http\Controllers\Operations\VehicleController::class, 'deleteDocument'])->name('fleet.documents.delete');
    
    // Drivers
    Route::resource('drivers', \App\Http\Controllers\Operations\DriverController::class);
    Route::post('drivers/{driver}/documents', [\App\Http\Controllers\Operations\DriverController::class, 'uploadDocument'])->name('drivers.documents.upload');
    Route::delete('drivers/{driver}/documents/{document}', [\App\Http\Controllers\Operations\DriverController::class, 'deleteDocument'])->name('drivers.documents.delete');
    
    // Compliance
    Route::get('/compliance', [\App\Http\Controllers\Operations\ComplianceController::class, 'index'])->name('compliance.index');
    
    // Reports
    Route::get('/reports', [\App\Http\Controllers\Operations\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Operations\ReportController::class, 'export'])->name('reports.export');
    
    // Commissions & Settlements
    Route::get('/commissions', [\App\Http\Controllers\Operations\CommissionController::class, 'index'])->name('commissions.index');
    Route::post('/commissions/generate', [\App\Http\Controllers\Operations\CommissionController::class, 'generate'])->name('commissions.generate');
    Route::get('/settlements', [\App\Http\Controllers\Operations\SettlementController::class, 'index'])->name('settlements.index');
    Route::post('/settlements/generate', [\App\Http\Controllers\Operations\SettlementController::class, 'generate'])->name('settlements.generate');
    Route::post('/settlements/{settlement}/approve', [\App\Http\Controllers\Operations\SettlementController::class, 'approve'])->name('settlements.approve');
    Route::post('/settlements/{settlement}/mark-paid', [\App\Http\Controllers\Operations\SettlementController::class, 'markPaid'])->name('settlements.mark-paid');
    
    // Partners
    Route::get('/partners', [\App\Http\Controllers\Operations\PartnerController::class, 'index'])->name('partners.index');
    Route::get('/partners/create', [\App\Http\Controllers\Operations\PartnerController::class, 'create'])->name('partners.create');
    Route::post('/partners', [\App\Http\Controllers\Operations\PartnerController::class, 'store'])->name('partners.store');
    Route::get('/partners/{partner}', [\App\Http\Controllers\Operations\PartnerController::class, 'show'])->name('partners.show');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\Operations\SettingsController::class, 'index'])->name('settings.index');
});

// Partner Portal Routes
Route::prefix('partner')->name('partner.')->middleware(['auth', 'verified', 'role:partner_admin,partner_operations_manager,partner_dispatcher,partner_finance_officer,partner_read_only_auditor'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Partner\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('bookings', \App\Http\Controllers\Partner\BookingController::class)->only(['index', 'show']);
    Route::post('bookings/{booking}/accept', [\App\Http\Controllers\Partner\BookingController::class, 'accept'])->name('bookings.accept');
    Route::post('bookings/{booking}/reject', [\App\Http\Controllers\Partner\BookingController::class, 'reject'])->name('bookings.reject');
    Route::get('/settlements', [\App\Http\Controllers\Partner\SettlementController::class, 'index'])->name('settlements.index');
    Route::get('/commissions', [\App\Http\Controllers\Partner\CommissionController::class, 'index'])->name('commissions.index');
    Route::patch('/commissions/frequency', [\App\Http\Controllers\Partner\CommissionController::class, 'updateFrequency'])->name('commissions.frequency');
    Route::post('/commissions/pay', [\App\Http\Controllers\Partner\CommissionController::class, 'pay'])->name('commissions.pay');
    Route::post('/commissions/{payment}/paid', [\App\Http\Controllers\Partner\CommissionController::class, 'markPaid'])->name('commissions.paid');
    Route::get('/fleet', [\App\Http\Controllers\Partner\VehicleController::class, 'index'])->name('fleet.index');
    Route::get('/drivers', [\App\Http\Controllers\Partner\DriverController::class, 'index'])->name('drivers.index');
});

// Driver Portal Routes
Route::prefix('driver')->name('driver.')->middleware(['auth', 'verified', 'role:driver'])->group(function () {
    Route::get('/dashboard', \App\Http\Controllers\Driver\DashboardController::class)->name('dashboard');
});

// Auth Routes
require __DIR__.'/auth.php';