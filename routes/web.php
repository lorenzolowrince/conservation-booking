<?php

use App\Http\Controllers\Admin\AreaController as AdminAreaController;
use App\Http\Controllers\Admin\BackupController as AdminBackupController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConservationAreaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Conservation Areas
Route::prefix('areas')->name('areas.')->group(function () {
    Route::get('/', [ConservationAreaController::class, 'index'])->name('index');
    Route::get('/{slug}', [ConservationAreaController::class, 'show'])->name('show');
});

// Booking (public)
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/create', [BookingController::class, 'create'])->name('create');
    Route::post('/store', [BookingController::class, 'store'])->name('store');
    Route::get('/confirmation/{ref}', [BookingController::class, 'confirmation'])->name('confirmation');
    Route::get('/track', [BookingController::class, 'track'])->name('track');
});

// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('booking.my-bookings');
    })->name('dashboard');

    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('booking.my-bookings');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [AdminBookingController::class, 'index'])->name('index');
        Route::get('/import', [AdminBookingController::class, 'importForm'])->name('import.form');
        Route::post('/import', [AdminBookingController::class, 'import'])->name('import');
        Route::get('/import/template', [AdminBookingController::class, 'downloadTemplate'])->name('import.template');
        Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
        Route::patch('/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('status');
        Route::patch('/{booking}/payment', [AdminBookingController::class, 'updatePayment'])->name('payment');
        Route::post('/{booking}/note', [AdminBookingController::class, 'addNote'])->name('note');
    });

    // Areas
    Route::prefix('areas')->name('areas.')->group(function () {
        Route::get('/', [AdminAreaController::class, 'index'])->name('index');
        Route::get('/create', [AdminAreaController::class, 'create'])->name('create');
        Route::post('/', [AdminAreaController::class, 'store'])->name('store');
        Route::get('/{area}', [AdminAreaController::class, 'show'])->name('show');
        Route::get('/{area}/edit', [AdminAreaController::class, 'edit'])->name('edit');
        Route::patch('/{area}', [AdminAreaController::class, 'update'])->name('update');
        Route::delete('/{area}', [AdminAreaController::class, 'destroy'])->name('destroy');
    });

    // Packages
    Route::resource('packages', AdminPackageController::class)->except(['show']);

    // Backup
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [AdminBackupController::class, 'index'])->name('index');
        Route::post('/', [AdminBackupController::class, 'create'])->name('create');
        Route::get('/{backup}/download', [AdminBackupController::class, 'download'])->name('download');
        Route::delete('/{backup}', [AdminBackupController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
