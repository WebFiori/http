<?php

require 'loader.php';

use webfiori\http\AbstractWebService;

/**
 * Description of HelloWorldService
 *
 * @author Ibrahim
 */
class HelloWorldService extends AbstractWebService {
    public function __construct() {
        parent::__construct('hello');
        $this->addRequestMethod('get');
    }
    public function isAuthorized() {
    }

    public function processRequest() {
        $this->getManager()->sendResponse('Hello World!');
    }
}
