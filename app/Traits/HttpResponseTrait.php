<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait HttpResponseTrait{
    public function success(string|array $data=array()): array|string{
        return $this->response(true, '01', 'Success', $data);
    }

    public function error(string $code='00', string|array $message_error=array(), string|array $data=array()){
        return $this->response(false, $code, $message_error, $data);
    }

    public function response(bool $status=false, string $cod_error='00', string|array $message_error='Unknown error', string|array $data=array()): array{
        return [
            'status'=>$status,
            'cod_error'=>$cod_error,
            'message_error'=>$message_error,
            'data'=>$data,
        ];
    }
}
