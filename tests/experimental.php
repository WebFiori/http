<?php
/*
 * A playground to do anything you like.
 */
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
require '../jsonx/JsonI.php';
require '../jsonx/JsonX.php';
require '../API.php';
require '../APIFilter.php';
require '../RequestParameter.php';
require '../APIAction.php';
use restEasy\APIFilter;
use restEasy\APIAction;
use restEasy\RequestParameter;
use restEasy\WebAPI;
class MyAPI extends WebAPI{
    public function __construct() {
        parent::__construct();
        $a = new APIAction('test');
        //$a->addRequestMethod('post');
        $r = new RequestParameter('hello');
        $a->addParameter($r);
        $this->addAction($a);
        $this->process();
    }
    public function isAuthorized() {
        
    }

    public function processRequest() {
        echo 'xxx<br/>';
        var_dump($this->getInputs());
    }

}
new MyAPI();