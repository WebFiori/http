<?php
require_once 'load.php';
use restEasy\WebAPI;
use restEasy\APIAction;
use restEasy\RequestParameter;
/**
 * An example that show to create a custom filter for an API parameter.
 */
class CustomFilter extends WebAPI{
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
            echo 'Parameter Name: '.$params->getName().'<br/>';
            return 3;
        },TRUE);
        $this->addAction($a1);
    }
    public function isAuthorized() {
        return TRUE;
    }

    public function processRequest() {
        print_r($this->getInputs());
    }

}
$c = new CustomFilter();
$c->process();

