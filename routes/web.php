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
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirigir raíz al dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Página offline (servida por service worker cuando no hay conexión)
Route::get('/offline', fn() => response()->file(public_path('offline.html')));

// ─── Rutas protegidas ────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rigs — ver y listar para todos; crear/editar/borrar solo admin
    Route::get('/rigs',           [RigController::class, 'index'])->name('rigs.index');
    Route::get('/rigs/{rig}',     [RigController::class, 'show'])->name('rigs.show');
    Route::middleware('admin')->group(function () {
        Route::get('/rigs/create',      [RigController::class, 'create'])->name('rigs.create');
        Route::post('/rigs',            [RigController::class, 'store'])->name('rigs.store');
        Route::get('/rigs/{rig}/edit',  [RigController::class, 'edit'])->name('rigs.edit');
        Route::put('/rigs/{rig}',       [RigController::class, 'update'])->name('rigs.update');
        Route::patch('/rigs/{rig}',     [RigController::class, 'update']);
        Route::delete('/rigs/{rig}',    [RigController::class, 'destroy'])->name('rigs.destroy');
    });

    // Pozos — solo admin
    Route::middleware('admin')->group(function () {
        Route::post('/rigs/{rig}/wells',              [WellController::class, 'store'])->name('rigs.wells.store');
        Route::delete('/rigs/{rig}/wells/{well}',     [WellController::class, 'destroy'])->name('rigs.wells.destroy');
    });

    // Bombas — ver para todos; crear/editar/borrar solo admin
    Route::get('/pumps',          [PumpController::class, 'index'])->name('pumps.index');
    Route::get('/pumps/{pump}',   [PumpController::class, 'show'])->name('pumps.show');
    Route::middleware('admin')->group(function () {
        Route::get('/pumps/create',       [PumpController::class, 'create'])->name('pumps.create');
        Route::post('/pumps',             [PumpController::class, 'store'])->name('pumps.store');
        Route::get('/pumps/{pump}/edit',  [PumpController::class, 'edit'])->name('pumps.edit');
        Route::put('/pumps/{pump}',       [PumpController::class, 'update'])->name('pumps.update');
        Route::patch('/pumps/{pump}',     [PumpController::class, 'update']);
        Route::delete('/pumps/{pump}',    [PumpController::class, 'destroy'])->name('pumps.destroy');
    });

    // Personal de bomba — solo admin
    Route::middleware('admin')->group(function () {
        Route::get('/pumps/{pump}/personnel',  [PersonnelController::class, 'create'])->name('pumps.personnel.create');
        Route::post('/pumps/{pump}/personnel', [PersonnelController::class, 'store'])->name('pumps.personnel.store');
    });

    // Registro diario — ver/crear para todos; editar solo admin
    Route::get('/pumps/{pump}/logs',             [DailyLogController::class, 'index'])->name('pumps.logs.index');
    Route::get('/pumps/{pump}/logs/create',      [DailyLogController::class, 'create'])->name('pumps.logs.create');
    Route::post('/pumps/{pump}/logs',            [DailyLogController::class, 'store'])->name('pumps.logs.store');
    Route::get('/pumps/{pump}/logs/{log}',       [DailyLogController::class, 'show'])->name('pumps.logs.show');
    Route::middleware('admin')->group(function () {
        Route::get('/pumps/{pump}/logs/{log}/edit', [DailyLogController::class, 'edit'])->name('pumps.logs.edit');
        Route::put('/pumps/{pump}/logs/{log}',      [DailyLogController::class, 'update'])->name('pumps.logs.update');
    });

    // Historial de reemplazos
    Route::get('/pumps/{pump}/replacements', [PumpController::class, 'replacements'])->name('pumps.replacements');

    // Reemplazo de componentes — todos pueden reemplazar
    Route::get('/pumps/{pump}/components/{component}/replace',  [ComponentController::class, 'replaceForm'])->name('components.replace.form');
    Route::post('/pumps/{pump}/components/{component}/replace', [ComponentController::class, 'replace'])->name('components.replace');

    // Alertas
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');

    // Reportes PDF
    Route::get('/reports',           [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Gestión de usuarios — solo admin
    Route::middleware('admin')->group(function () {
        Route::get('/users',           [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',    [UserController::class, 'create'])->name('users.create');
        Route::post('/users',          [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',       [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',            [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle',   [UserController::class, 'toggle'])->name('users.toggle');
        Route::delete('/users/{user}',         [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Perfil
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
