<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model{
    use HasFactory;

    protected $fillable=[
        'code',
        'fk_id_users',
        'fk_id_wallets',
        'type',
        'amount',
        'description',
    ];
}
