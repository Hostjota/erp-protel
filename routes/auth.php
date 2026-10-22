<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArtigoController;
use App\Http\Controllers\EncomendaController;

Route::middleware('guest')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Reativado para processar o login real com a base de dados
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    //Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/encomendas', [EncomendaController::class, 'index'])->name('encomendas.index');
    Route::get('/encomendas/{nDoc}/pdf', [App\Http\Controllers\EncomendaController::class, 'pdf'])->name('encomendas.pdf');
    Route::get('/encomendas/sincronizar', [EncomendaController::class, 'sincronizar'])->name('encomendas.sincronizar');

    Route::get('/artigos/resync', [ArtigoController::class, 'resync'])->name('artigos.resync');
    Route::get('/artigos/novo', [ArtigoController::class, 'create'])->name('artigos.create');
    Route::post('/artigos', [ArtigoController::class, 'store'])->name('artigos.store');
    Route::get('/artigos', [ArtigoController::class, 'index'])->name('artigos.index');
    Route::get('/familias', [FamiliaController::class, 'index'])->name('familias.index');

    /*==============================utilizador===================================*/

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::delete('/utilizadores/{user}', [RegisteredUserController::class, 'destroy'])->name('utilizadores.destroy');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
