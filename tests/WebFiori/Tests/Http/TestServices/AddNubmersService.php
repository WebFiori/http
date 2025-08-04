<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\RequestMethod;
use WebFiori\Http\RequestParameter;
/**
 * Description of AddNubmersService
 *
 * @author Ibrahim
 */
class AddNubmersService extends AbstractNumbersService {
    public function __construct() {
        parent::__construct('add-two-integers');
        $this->setDescription('Returns a JSON string that has the sum of two integers.');
        $this->addRequestMethod(RequestMethod::GET);
        
        $this->addParameter(new RequestParameter('first-number', 'integer'));
        $this->addParameter(new RequestParameter('second-number', 'integer'));
    }


    public function processRequest() {
        $firstNum = $this->getParamVal('first-number');
        $secondNumber = $this->getParamVal('second-number');
        $sum = $firstNum+$secondNumber;
        $this->sendResponse('The sum of '.$firstNum.' and '.$secondNumber.' is '.$sum.'.');
    }

}
