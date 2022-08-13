<?php

namespace App\Libs;

use App\Models\Movement;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Bill{

    function __construct(){ }

    public static function createWallet($idUser): Wallet{
        $wallet=Wallet::query()->create([
            'fk_id_users'=>$idUser,
            'cod_wallet'=>'WAL-'.Carbon::now()->format('ymdhis'),
            'amount'=>0,
        ]);

        return $wallet;
    }

    public static function createMovement(int $idUser, int $idWallet, float $amount, $type='inbound', $description=null): Movement{
        $amount = abs($amount);
        $movement=Movement::query()->create([
                'code'=>uniqid(),
                'fk_id_users'=>$idUser,
                'fk_id_wallets'=>$idWallet,
                'type'=>$type,
                'amount'=>$amount,
                'description'=>$description,
            ]);
        if($movement){
            self::UpdateMovementWallet($idWallet,$amount,$type);
        }
        return $movement;
    }

    private static function UpdateMovementWallet(int $idWallet,float $amount,string $type){
        $wallet = Wallet::query()->find($idWallet);
        if($wallet){
            if($type == 'inbound'){
                $total = price($wallet->amount+$amount);
            }else{
                $total = price($wallet->amount-$amount);
            }
            $wallet->amount = $total;
            $wallet->touch();
        }
    }
}
