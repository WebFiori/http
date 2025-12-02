<?php

require 'loader.php';

use WebFiori\Http\WebService;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;

class HelloWorldService extends WebService {
    public function __construct() {
        parent::__construct('hello');
        $this->setRequestMethods([RequestMethod::GET]);
        
        $this->addParameters([
            'my-name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }
    public function isAuthorized() {
    }

    public function processRequest() {
        $name = $this->getParamVal('my-name');
        
        if ($name !== null) {
            $this->sendResponse("Hello '$name'.");
        }
        $this->sendResponse('Hello World!');
    }
}
