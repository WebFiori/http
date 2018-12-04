<?php
require_once 'load.php';
use restEasy\WebAPI;
use restEasy\APIAction;
use restEasy\RequestParameter;
use jsonx\JsonX;
/**
 * An API that is used to show how to use booleans in API calls.
 *
 * @author Ibrahim
 */
class BooleanAPI extends WebAPI{
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
