<?php

require 'loader.php';

use webfiori\restEasy\WebService;

/**
 * Description of HelloWorldService
 *
 * @author Ibrahim
 */
class HelloWorldService extends WebService {
    public function __construct() {
        parent::__construct('hello');
        $this->addRequestMethod('get');
    }
    public function isAuthorized() {
        
    }

    public function processRequest($inputs) {
        $this->getManager()->sendResponse('Hello World!');
    }

}
