<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RigController;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\WellController;
use Illuminate\Support\Facades\Route;

// Redirigir raíz al dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Página offline (servida por service worker cuando no hay conexión)
Route::get('/offline', fn() => response()->file(public_path('offline.html')));

// ─── Rutas protegidas ────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rigs
    Route::resource('rigs', RigController::class);

    // Pozos (dentro de un rig)
    Route::post('/rigs/{rig}/wells', [WellController::class, 'store'])->name('rigs.wells.store');
    Route::delete('/rigs/{rig}/wells/{well}', [WellController::class, 'destroy'])->name('rigs.wells.destroy');

    // Bombas
    Route::resource('pumps', PumpController::class);

    // Personal de bomba
    Route::get('/pumps/{pump}/personnel', [PersonnelController::class, 'create'])->name('pumps.personnel.create');
    Route::post('/pumps/{pump}/personnel', [PersonnelController::class, 'store'])->name('pumps.personnel.store');

    // Registro diario
    Route::get('/pumps/{pump}/logs',             [DailyLogController::class, 'index'])->name('pumps.logs.index');
    Route::get('/pumps/{pump}/logs/create',      [DailyLogController::class, 'create'])->name('pumps.logs.create');
    Route::post('/pumps/{pump}/logs',            [DailyLogController::class, 'store'])->name('pumps.logs.store');
    Route::get('/pumps/{pump}/logs/{log}',       [DailyLogController::class, 'show'])->name('pumps.logs.show');
    Route::get('/pumps/{pump}/logs/{log}/edit',  [DailyLogController::class, 'edit'])->name('pumps.logs.edit');
    Route::put('/pumps/{pump}/logs/{log}',       [DailyLogController::class, 'update'])->name('pumps.logs.update');

    // Reemplazo de componentes
    Route::get('/pumps/{pump}/components/{component}/replace',  [ComponentController::class, 'replaceForm'])->name('components.replace.form');
    Route::post('/pumps/{pump}/components/{component}/replace', [ComponentController::class, 'replace'])->name('components.replace');

    // Alertas
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');

    // Reportes PDF
    Route::get('/reports',          [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate',[ReportController::class, 'generate'])->name('reports.generate');

    // Perfil
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
