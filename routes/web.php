<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\ArtigoController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArtigoSyncController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactoController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas do E-Commerce
|--------------------------------------------------------------------------
*/

Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto/enviar', [ContactoController::class, 'enviar'])->name('contacto.enviar');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [HomeController::class, 'sobre'])->name('sobre');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/sucesso/{referencia}', [CheckoutController::class, 'sucesso'])->name('checkout.sucesso');

Route::get('/loja', [LojaController::class, 'index'])->name('loja.index');
Route::get('/produto/{codigo}', [LojaController::class, 'show'])->name('loja.show');
Route::get('/erp-imagem/{nArtigo}', [LojaController::class, 'servirImagem'])->name('erp.imagem');

// Carrinho de Compras
Route::prefix('carrinho')->name('carrinho.')->group(function () {
    Route::get('/', [CarrinhoController::class, 'index'])->name('index');
    Route::post('/adicionar', [CarrinhoController::class, 'add'])->name('add');
    Route::post('/atualizar', [CarrinhoController::class, 'update'])->name('update');
    Route::post('/remover', [CarrinhoController::class, 'remove'])->name('remove');
    Route::post('/limpar', [CarrinhoController::class, 'clear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas do Painel de Administração (Com Níveis de Acesso)
|--------------------------------------------------------------------------
*/

// 1. Rotas acessíveis por QUALQUER utilizador autenticado no painel (user, admin e superadmin)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/encomendas', [EncomendaController::class, 'index'])->name('encomendas.index');
});

// 2. Rotas exclusivas para Admin e Superadmin (criar, editar, sincronizar)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/encomendas/sincronizar', [EncomendaController::class, 'sincronizar'])->name('encomendas.sincronizar');

    Route::get('/artigos/novo', [ArtigoController::class, 'create'])->name('artigos.create');
    Route::post('/artigos', [ArtigoController::class, 'store'])->name('artigos.store');
    Route::get('/artigos/resync', [ArtigoController::class, 'resync'])->name('artigos.resync');
});

// 3. Rotas exclusivas para Superadmin (definições críticas / gestão de equipa)
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // Exemplo: Gestão de utilizadores do sistema
    Route::get('/familias', [FamiliaController::class, 'index'])->name('familias.index');


});

// Rota para ver o perfil
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/utilizadores', [ProfileController::class, 'index'])->name('utilizadores.index');

});

require __DIR__.'/auth.php';