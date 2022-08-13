<?php

namespace App\Libs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestValidate{

    protected string $structuredRequest='json';
    protected $validator;
    protected $data;
    protected $post;

    public function __construct(protected Request $request, protected $rules=array(), protected $messages=array()){
        $this->post=$this->request->getContent();
        $this->decodeData();
    }

    private function decodeData(){
        if($this->request->isJson()){
            $this->data=json_decode($this->post, true);
        }
        else{
            $this->structuredRequest='soap';
        }
    }

    public function isValid(){
        $this->validator=Validator::make($this->data, $this->rules);
        return !$this->validator->fails();
    }

    public function failMessages(){
        $messages=json_decode($this->validator->messages());
        $result=array();
        foreach($messages as $message){
            if(is_array($message)){
                $result[]=implode(",",$message);
            }
        }
        return $result;
    }

}
