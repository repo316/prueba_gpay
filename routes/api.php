<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\{
    BillingController,
    RegisterController,
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/registro/cliente', [
    RegisterController::class,
    'RegistroCliente'
])->name('register.client');

Route::name('billetera.')->prefix("billetera")->group(function(){
    Route::post('/cargar', [
        BillingController::class,
        'RecargaBilletera'
    ])->name('register.movement');

    Route::post('/generar/pago', [
        BillingController::class,
        'Pagar'
    ])->name('register.payment');

    Route::post('/confirmar/{token}/pago', [
        BillingController::class,
        'ConfirmarPago'
    ])->name('confirm.payment');

    Route::post('/saldo', [
        BillingController::class,
        'ConsultarSaldo'
    ])->name('view.amount');
});
