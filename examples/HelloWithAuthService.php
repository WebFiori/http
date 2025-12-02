<?php

require 'loader.php';

use WebFiori\Http\WebService;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\ResponseMessage;

class HelloWithAuthService extends WebService {
    public function __construct() {
        parent::__construct('hello-with-auth');
        $this->setRequestMethods([RequestMethod::GET]);
        
        $this->addParameters([
            'my-name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }
    public function isAuthorized(): bool {
        //Change default response message to custom one
        ResponseMessage::set('401', 'Not authorized to use this API.');
        
        $authHeader = $this->getAuthHeader();
        
        if ($authHeader === null) {
            return false;
        }
        
        $scheme = $authHeader->getScheme();
        $credentials = $authHeader->getCredentials();
        
        if ($scheme != 'bearer') {
            return false;
        }
        
        return $credentials == 'abc123trX';
    }

    public function processRequest() {
        $name = $this->getParamVal('my-name');
        
        if ($name !== null) {
            $this->sendResponse("Hello '$name'.");
        }
        $this->sendResponse('Hello World!');
    }
}
