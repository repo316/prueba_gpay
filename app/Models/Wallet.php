<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model{
    use HasFactory;

    protected $fillable=[
        'fk_id_users',
        'cod_wallet',
        'amount',
    ];
}
