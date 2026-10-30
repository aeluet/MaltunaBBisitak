<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AvailableDateController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CloseVisitController;
use App\Http\Controllers\ContractorShiftController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitAttendanceController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VisitTypeController;
use App\Http\Controllers\WalkInController;
use App\Http\Middleware\SetPublicLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Web orri publikoa: edonondik eskuragarri (WP001), hiru hizkuntzatan (WP009).
Route::get('/', fn (Request $request) => redirect()->route('bookings.create', [
    'locale' => $request->getPreferredLanguage(SetPublicLocale::LOCALES),
]));

Route::prefix('{locale}')
    ->whereIn('locale', SetPublicLocale::LOCALES)
    ->middleware(SetPublicLocale::class)
    ->group(function () {
        Route::get('/', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/', [BookingController::class, 'store'])->middleware('throttle:bookings')->name('bookings.store');
        Route::get('eskerrik-asko', [BookingController::class, 'thanks'])->name('bookings.thanks');
    });

// Langileen eremua: ikastetxeko saretik bakarrik (AP001).
Route::middleware('internal.network')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware(['auth', 'active'])->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('panela', DashboardController::class)->name('dashboard');

        Route::middleware('role:admin,harrera')->group(function () {
            Route::get('sarrera-berria', [WalkInController::class, 'create'])->name('walk-ins.create');
            Route::post('sarrera-berria', [WalkInController::class, 'store'])->name('walk-ins.store');

            Route::post('bertaratzeak/{attendance}/sarrera', [VisitAttendanceController::class, 'checkIn'])
                ->name('attendances.check-in');
            Route::post('bertaratzeak/{attendance}/irteera', [VisitAttendanceController::class, 'checkOut'])
                ->name('attendances.check-out');
            Route::get('bisitak', [VisitController::class, 'index'])->name('visits.index');
            Route::get('bisitak/{visit}', [VisitController::class, 'show'])->name('visits.show');
            Route::post('bisitak/{visit}/onartu', [VisitController::class, 'approve'])->name('visits.approve');
            Route::post('bisitak/{visit}/ukatu', [VisitController::class, 'reject'])->name('visits.reject');
            Route::post('bisitak/{visit}/bertan-behera', [VisitController::class, 'cancel'])->name('visits.cancel');
            Route::post('bisitak/{visit}/itxi', CloseVisitController::class)->name('visits.close');

            Route::resource('bisitariak', VisitorController::class)
                ->except('destroy')
                ->parameters(['bisitariak' => 'visitor'])
                ->names('visitors');

            Route::resource('bilerak', MeetingController::class)
                ->parameters(['bilerak' => 'meeting'])
                ->names('meetings');

            Route::resource('kanpo-langileak', ContractorShiftController::class)
                ->only(['index', 'create', 'store'])
                ->parameters(['kanpo-langileak' => 'contractorShift'])
                ->names('contractor-shifts');
            Route::post('kanpo-langileak/{contractorShift}/irteera', [ContractorShiftController::class, 'checkOut'])
                ->name('contractor-shifts.check-out');
            Route::get('kanpo-langileak/{contractorShift}/sinadura', [ContractorShiftController::class, 'signature'])
                ->name('contractor-shifts.signature');
        });

        Route::middleware('role:admin')->group(function () {
            Route::delete('bisitariak/{visitor}', [VisitorController::class, 'destroy'])->name('visitors.destroy');

            Route::resource('erabiltzaileak', UserController::class)
                ->except(['show', 'destroy'])
                ->parameters(['erabiltzaileak' => 'user'])
                ->names('users');

            Route::resource('egutegia', AvailableDateController::class)
                ->except(['create', 'show'])
                ->parameters(['egutegia' => 'availableDate'])
                ->names('available-dates');

            Route::resource('bisita-motak', VisitTypeController::class)
                ->except(['show', 'destroy'])
                ->parameters(['bisita-motak' => 'visitType'])
                ->names('visit-types');
        });
    });
});
