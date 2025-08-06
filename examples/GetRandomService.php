<?php

require 'loader.php';

use WebFiori\Http\AbstractWebService;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;

class GetRandomService extends AbstractWebService {
    public function __construct() {
        parent::__construct('get-random-number');
        $this->setRequestMethods([
            RequestMethod::GET, 
            RequestMethod::POST
        ]);
        
        $this->addParameters([
            'min' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true
            ],
            'max' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true
            ]
        ]);
    }

    public function isAuthorized() {
//        $authHeader = $this->getAuthHeader();
//        
//        if ($authHeader === null) {
//            return false;
//        }
//        
//        $scheme = $authHeader->getScheme();
//        $credentials = $authHeader->getCredentials();
        
        //Verify credentials based on auth scheme (e.g. 'Basic', 'Barear'
    }

    public function processRequest() {
        $max = $this->getParamVal('max');
        $min = $this->getParamVal('min');

        if ($max !== null && $min !== null) {
            $random = rand($min, $max);
        } else {
            $random = rand();
        }
        $this->sendResponse($random);
    }
}
