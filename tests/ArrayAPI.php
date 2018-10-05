<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'load.php';
/**
 * Description of IntegerAPI
 *
 * @author Ibrahim
 */
class IntegerAPI extends API{
    public function __construct() {
        parent::__construct();
        $a1 = new APIAction('sum-array');
        $a1->addRequestMethod('get');
        $a1->addParameter(new RequestParameter('numbers', 'array'));
        $this->addAction($a1);
    }
    public function isAuthorized() {
        return TRUE;
    }

    public function processRequest() {
        $i = $this->getInputs();
        $j = new JsonX();
        $j->add('numbers', $i['numbers']);
        $sum = 0;
        foreach ($i['numbers'] as $number){
            $type = gettype($number);
            if($type == 'integer' || $type == 'double'){
                $sum += $number;
            }
        }
        $j->add('sum', $sum);
        $this->sendResponse('All Ok', FALSE, 200, '"details":'.$j);
    }

}
$i = new IntegerAPI();
$i->process();