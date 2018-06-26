<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
require_once '../jsonx-1.3/JsonI.php';
require_once '../jsonx-1.3/JsonX.php';
require_once '../API.php';
require_once '../APIAction.php';
require_once '../APIFilter.php';
require_once '../RequestParameter.php';
class CustomFilter extends API{
    public function __construct() {
        parent::__construct();
        $a1 = new APIAction('is-good-boy');
        $a1->addRequestMethod('get');
        $a1->addParameter(new RequestParameter('bad', 'integer'));
        $a1->getParameterByName('bad')->setCustomFilterFunction(function($val,$params){
            $basicFilterResult = $val['basic-filter-result'];
            $originalVal = $val['original-value'];
            echo 'Basic filter result: '.$basicFilterResult.'<br/>';
            echo 'Original value: '.$originalVal.'<br/>';
            echo 'Parameter Name: '.$params->getName();
            return 3;
        },TRUE);
        $this->addAction($a1);
    }
    public function isAuthorized() {
        return TRUE;
    }

    public function processRequest() {
        //print_r($this->getInputs());
    }

}
$c = new CustomFilter();
$c->process();

