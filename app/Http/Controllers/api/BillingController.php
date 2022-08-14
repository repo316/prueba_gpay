<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Libs\Bill;
use App\Libs\RequestValidate;
use App\Mail\ConfirmEmail;
use App\Mail\ConfirmPaymentEmail;
use App\Models\movement;
use App\Models\Payment;
use App\Models\TokenSession;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\HttpResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BillingController extends Controller{
    use HttpResponseTrait;

    public function RecargaBilletera(Request $request){
        $result=$this->defaultError();
        $validate=new RequestValidate($request, [
            'Documento'=>[
                'required',
                'min:5'
            ],
            'Celular'=>[
                'required',
                'numeric',
                'min:5'
            ],
            'Valor'=>[
                'required',
                'numeric',
                'min:5'
            ],
        ]);

        if($validate->isValid()){
            DB::beginTransaction();
            $document=$request->get('Documento');
            $phone=$request->get('Celular');
            $amount=(float)$request->get('Valor');
            $description=$request->get('Descripcion', 'Recarga de billetera');
            try{
                $user=User::query()->select([
                    'users.id',
                    'ws.id as wallet'
                ])->join('wallets as ws', 'ws.fk_id_users', '=', 'users.id', 'inner')->FindByDocumentPhone($document, $phone)->first();
                if($user){
                    $bill=Bill::createMovement($user->id, $user->wallet, $amount, 'inbound', $description);
                    if($bill->id){
                        $result=$this->success($bill->code);
                        DB::commit();
                    }
                    else{
                        $result=$this->error('03', 'The Transaction has failed');
                        DB::rollBack();
                    }
                }
                else{
                    $result=$this->error('04', 'User not found');
                    DB::rollBack();
                }
            }catch(\Exception $e){
                DB::rollBack();
            }catch(\Illuminate\Database\QueryException $e){
                DB::rollBack();
            }
        }
        else{
            $result=$this->error('02', $validate->failMessages());
        }

        if($validate->getRequestType()=='json'){
            return response()->json($result, 200);
        }
        else{
            return $this->soapResponse($result);
        }
    }

    public function Pagar(Request $request){
        $result=$this->defaultError();
        $validate=new RequestValidate($request, [
            'Documento'=>[
                'required',
                'min:5'
            ],
            'Celular'=>[
                'required',
                'numeric',
                'min:5'
            ],
            'Valor'=>[
                'required',
                'numeric',
                'min:5'
            ],
            'Descripcion'=>[
                'required',
                'min:4'
            ],
        ]);

        if($validate->isValid()){
            DB::beginTransaction();
            $document=$request->get('Documento');
            $phone=$request->get('Celular');
            $amount=(float)$request->get('Valor');
            $description=$request->get('Descripcion');
            try{
                $user=User::query()->select([
                    'users.id',
                    'users.email',
                    'users.name',
                    'ws.id as wallet',
                    'ws.amount'
                ])->join('wallets as ws', 'ws.fk_id_users', '=', 'users.id', 'inner')->FindByDocumentPhone($document, $phone)->first();

                if($user){
                    if($user->amount>$amount){
                        $payment=Payment::query()->create([
                            'fk_id_users'=>$user->id,
                            'description'=>$description,
                            'amount'=>price($amount),
                        ]);
                        if($payment){
                            $session=uniqid();
                            $token=str_shuffle(Str::random(5).$payment->id);
                            $tokens=TokenSession::query()->create([
                                'fk_id_users'=>$user->id,
                                'fk_id_payment'=>$payment->id,
                                'session'=>$session,
                                'token'=>$token,
                                'date_end'=>Carbon::now()->addDay()->format('Y-m-d H:i:s'),
                            ]);

                            if($tokens){
                                $result=$this->success(['session'=>$session]);
                                DB::commit();
                                Mail::to($user->email)->send(new ConfirmPaymentEmail($user->name, $token));
                            }
                            else{
                                $result=$this->error('05', 'Error generating payment token payment');
                                DB::rollBack();
                            }
                        }
                        else{
                            $result=$this->error('04', 'Error creating payment transaction');
                            DB::rollBack();
                        }
                    }
                    else{
                        $result=$this->error('03', 'The wallet amount is less than the payment');
                        DB::rollBack();
                    }
                }
            }catch(\Exception $e){
                DB::rollBack();
            }catch(\Illuminate\Database\QueryException $e){
                DB::rollBack();
            }
        }
        else{
            $result=$this->error('02', $validate->failMessages());
        }

        if($validate->getRequestType()=='json'){
            return response()->json($result, 200);
        }
        else{
            return $this->soapResponse($result);
        }
    }

    public function ConfirmarPago(Request $request, $token){
        $result=$this->defaultError();
        $validate=new RequestValidate($request, [
            'Documento'=>[
                'required',
                'min:5'
            ],
            'Celular'=>[
                'required',
                'numeric',
                'min:5'
            ],
            'Session'=>[
                'required',
                'min:5',
            ],
        ]);

        if($validate->isValid()){
            $tokens=TokenSession::query()->where(function($query) use ($token, $request){
                $query->where('token', '=', $token);
                $query->where('session', '=', $request->get('Session'));
                $query->where('date_end', '>=', Carbon::now()->format('Y-m-d H:i:s'));
            })->first();

            if($tokens){
                $payment=Payment::query()->where('status', '=', 'inactive')->find($tokens->fk_id_payment);
                if($payment){
                    $document=$request->get('Documento');
                    $phone=$request->get('Celular');
                    $payment->update(['status'=>'active']);
                    $user=User::query()->select([
                        'users.id',
                        'ws.id as wallet'
                    ])->join('wallets as ws', 'ws.fk_id_users', '=', 'users.id', 'inner')->FindByDocumentPhone($document, $phone)->first();
                    $bill=Bill::createMovement($user->id, $user->wallet, (float)$payment->amount, 'outbound', 'Pago '.$payment->description);
                    if($bill){
                        $result=$this->success($bill->code);
                        $tokens->touch();
                    }
                }
                else{
                    $result=$this->error('04', 'Error processing your payment');
                }
            }
            else{
                $result=$this->error('03', 'Token not found');
            }
        }
        else{
            $result=$this->error('02', $validate->failMessages());
        }

        if($validate->getRequestType()=='json'){
            return response()->json($result, 200);
        }
        else{
            return $this->soapResponse($result);
        }
    }

    public function ConsultarSaldo(Request $request){
        $result=$this->defaultError();
        $validate=new RequestValidate($request, [
            'Documento'=>[
                'required',
                'min:5'
            ],
            'Celular'=>[
                'required',
                'numeric',
                'min:5'
            ],
        ]);

        if($validate->isValid()){
            $document=$request->get('Documento');
            $phone=$request->get('Celular');
            $user=User::query()->select([
                'users.email',
                'users.name',
                'ws.amount'
            ])->join('wallets as ws', 'ws.fk_id_users', '=', 'users.id', 'inner')->FindByDocumentPhone($document, $phone)->first();

            if($user){
                $result=$this->success([
                    'Saldo'=>price($user->amount),
                    'Nombre'=>$user->name,
                    'Email'=>$user->email
                ]);
            }
        }
        else{
            $result=$this->error('02', $validate->failMessages());
        }

        if($validate->getRequestType()=='json'){
            return response()->json($result, 200);
        }
        else{
            return $this->soapResponse($result);
        }
    }
}
