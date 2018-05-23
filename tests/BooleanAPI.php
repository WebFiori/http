<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
require_once '../jsonx-1.3/JsonI.php';
require_once '../jsonx-1.3/JsonX.php';
require_once '../API.php';
require_once '../APIAction.php';
require_once '../APIFilter.php';
require_once '../RequestParameter.php';
/**
 * Description of BooleanAPI
 *
 * @author Ibrahim
 */
class BooleanAPI extends API{
    public function __construct() {
        parent::__construct();
        $a1 = new APIAction('add-user');
        $a1->addRequestMethod('get');
        $a1->addParameter(new RequestParameter('is-admin', 'boolean'));
        $this->addAction($a1);
    }
    public function isAuthorized() {
        return TRUE;
    }

    public function processRequest() {
        $a = $this->getAction();
        $i = $this->getInputs();
        $j = new JsonX();
        $j->add('given-val', $i['is-admin']);
        $this->sendResponse('All Ok', FALSE, 200, '"response":'.$j);
    }

}
$b = new BooleanAPI();
$b->process();
