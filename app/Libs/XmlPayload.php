<?php
namespace App\Libs;

use Carbon\Carbon;
use Sabre\Xml\Service;

class XmlPayload{

	private $payload;
	protected $service;

	function __construct(){
		$this->service=new Service();
	}

	function setXML($payload){
		$parse=$this->service->parse($payload);
		$payloadArray=$parse[0]['value'];
        foreach($payloadArray as $p){
            $this->payload[$this->cleanKey($p['name'])] = $this->cleanValue($p['value']);
        }
		unset($parse);
		return $this;
	}

    private function cleanKey($key){
        return str_replace('{}','',$key);
    }

    private function cleanValue($val){
        return str_replace(["\n","\s"],'',$val);
    }

	/**
	 * @return mixed
	 */
	public function getPayload(){
		return $this->payload;
	}

}
