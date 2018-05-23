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
        $a1 = new APIAction('add-numbers');
        $a1->addRequestMethod('get');
        $a1->addParameter(new RequestParameter('num-1', 'integer'));
        $a1->getParameterByName('num-1')->setDefault(100);
        $a1->addParameter(new RequestParameter('num-2', 'integer'));
        $a1->getParameterByName('num-2')->setMaxVal(10000);
        $this->addAction($a1);
        
        $this->setVersion('1.0.1');
        $a1 = new APIAction('sub-numbers');
        $a1->addRequestMethod('get');
        $a1->addParameter(new RequestParameter('num-1', 'integer'));
        $a1->getParameterByName('num-1')->setDefault(100);
        $a1->addParameter(new RequestParameter('num-2', 'integer'));
        $a1->getParameterByName('num-2')->setMaxVal(10000);
        $this->addAction($a1);
        
        
    }
    public function isAuthorized() {
        return TRUE;
    }

    public function processRequest() {
        $i = $this->getInputs();
        $j = new JsonX();
        $j->add('first-num', $i['num-1']);
        $j->add('sec-num', $i['num-2']);
        if($this->getAction() == 'add-numbers'){
            $j->add('sum', ($i['num-1'] + $i['num-2']));
        }
        else if($this->getAction() == 'add-numbers'){
            $j->add('sub', ($i['num-1'] - $i['num-2']));
        }
        $this->sendResponse('All Ok', FALSE, 200, '"sum":'.$j);
    }

}
$i = new IntegerAPI();
$i->process();
