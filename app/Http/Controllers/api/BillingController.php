<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Libs\Bill;
use App\Libs\RequestValidate;
use App\Models\movement;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller{
    use HttpResponseTrait;

    public function RecargaBilletera(Request $request){
        $result = $this->defaultError();
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
            ],
        ]);

        if($validate->isValid()){
            DB::beginTransaction();
            $document = $request->get('Documento');
            $phone = $request->get('Celular');
            $amount = (float)$request->get('Valor');
            $description = $request->get('Descripcion',null);
            try{
                $user = User::query()->select(['users.id','ws.id as wallet'])->join('wallets as ws','ws.fk_id_users','=','users.id','inner')->FindByDocumentPhone($document,$phone)->first();
                if($user){
                    $type='inbound';
                    if($amount<0){
                        $type='outbound';
                    }
                    $bill = Bill::createMovement($user->id, $user->wallet, $amount,$type,$description);
                    if($bill->id){
                        $result = $this->success($bill->code);
                        DB::commit();
                    }else{
                        $result = $this->error('03','The Transaction has failed');
                        DB::rollBack();
                    }
                }else{
                    $result = $this->error('04','User not found');
                    DB::rollBack();
                }
            }catch(\Exception $e){
                DB::rollBack();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
            }
        }else{
            $result = $this->error('02',$validate->failMessages());
        }

        return response()->json($result);
    }


}
