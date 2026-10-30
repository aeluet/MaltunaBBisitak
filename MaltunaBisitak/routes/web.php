<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VisitTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
            Route::resource('bisitariak', VisitorController::class)
                ->except('destroy')
                ->parameters(['bisitariak' => 'visitor'])
                ->names('visitors');
        });

        Route::middleware('role:admin')->group(function () {
            Route::delete('bisitariak/{visitor}', [VisitorController::class, 'destroy'])->name('visitors.destroy');

            Route::resource('erabiltzaileak', UserController::class)
                ->except(['show', 'destroy'])
                ->parameters(['erabiltzaileak' => 'user'])
                ->names('users');

            Route::resource('bisita-motak', VisitTypeController::class)
                ->except(['show', 'destroy'])
                ->parameters(['bisita-motak' => 'visitType'])
                ->names('visit-types');
        });
    });
});
