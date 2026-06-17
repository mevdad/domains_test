<?php

use App\Http\Controllers\ContactFormController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::get('/form', [ContactFormController::class, 'show'])->name('contact-form.show');
Route::post('/form', [ContactFormController::class, 'submit'])->name('contact-form.submit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/domains.php';
