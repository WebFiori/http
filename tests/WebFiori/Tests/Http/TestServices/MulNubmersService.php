<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\RequestParameter;
/**
 *
 * @author Ibrahim
 */
class MulNubmersService extends WebService {
    public function __construct() {
        parent::__construct('mul-two-integers');
        $this->setDescription('Returns a JSON string that has the multiplication of two integers.');
        $this->addRequestMethod(RequestMethod::GET);
        
        $this->addParameter(new RequestParameter('first-number', 'integer'));
        $this->addParameter(new RequestParameter('second-number', 'integer'));
    }
    
    public function isAuthorizedGET() {
        if ($this->getParamVal('first-number') < 0) {
            return false;
        }
    }

    public function processGet() {
        $firstNum = $this->getParamVal('first-number');
        $secondNumber = $this->getParamVal('second-number');
        $sum = $firstNum*$secondNumber;
        $this->sendResponse('The multiplication of '.$firstNum.' and '.$secondNumber.' is '.$sum.'.');
    }

    public function processRequest() {
        
    }
}
