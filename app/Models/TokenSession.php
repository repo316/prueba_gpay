<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenSession extends Model{
    use HasFactory;

    protected $fillable=[
        'fk_id_users',
        'fk_id_payment',
        'session',
        'token',
        'date_end',
    ];
}
