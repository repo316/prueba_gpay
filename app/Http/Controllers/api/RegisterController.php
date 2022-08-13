<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Libs\RequestValidate;
use App\Mail\ConfirmEmail;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponseTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use HttpResponseTrait;

    public function Register(Request $request){
        $validate = new RequestValidate($request,[
            'Documento'=>[
                'required'
            ],
            'Nombres'=>[
                'required',
                'min:2'
            ],
            'Email'=>[
                'required',
                'email'
            ],
            'Celular'=>[
                'required',
                'numeric',
                'min:5'
            ]
        ]);

        if($validate->isValid()){
            $email = Str::lower($request->get('Email'));
            $user = User::query()->where('email', $email)->first();
            if(!$user){
                $data=[
                    'name'=>$request->get('Nombres'),
                    'email'=>$email,
                    'password'=>Str::random(8),
                    'document'=>$request->get('Documento'),
                    'phone'=>$request->get('Celular'),
                    'token'=>Str::random(16),
                ];

                $resUser = User::create($data);
                if($resUser){
                    Mail::to($email)->send(new ConfirmEmail($data['name'],$data['token']));
                    $result = $this->success($data['token']);
                }else{
                    $result = $this->error('04','User not created');
                }
            }else{
                $result = $this->error('03','User founded');
            }
        }else{
            $result = $this->error('02',$validate->failMessages());
        }
        return response()->json($result,200);
    }
}
