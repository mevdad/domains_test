<?php

use App\Http\Controllers\CheckLogController;
use App\Http\Controllers\DomainCheckController;
use App\Http\Controllers\DomainController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('domains', [DomainController::class, 'index'])->name('domains.index');
    Route::post('domains', [DomainController::class, 'store'])->name('domains.store');
    Route::patch('domains/{domain}', [DomainController::class, 'update'])->name('domains.update');
    Route::delete('domains/{domain}', [DomainController::class, 'destroy'])->name('domains.destroy');
    Route::get('domains/{domain}/checks', [DomainCheckController::class, 'index'])->name('domains.checks.index');
    Route::get('logs', [CheckLogController::class, 'index'])->name('logs.index');
});
