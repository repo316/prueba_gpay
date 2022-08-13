<?php

use App\Http\Controllers\api\BillingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\api\RegisterController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/registro/cliente', [RegisterController::class, 'RegistroCliente'])->name('register.client');
Route::post('/billetera/cargar', [BillingController::class, 'RecargaBilletera'])->name('register.movement');
Route::post('/billetera/generar/pago', [BillingController::class, 'Pagar'])->name('register.payment');
Route::post('/billetera/confirmar/{token}/pago', [BillingController::class, 'ConfirmarPago'])->name('confirm.payment');
Route::post('/billetera/saldo', [BillingController::class, 'ConsultarSaldo'])->name('view.amount');
