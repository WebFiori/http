<?php

namespace restEasy\tests;
use webfiori\http\RequestParameter;
/**
 * Description of AddNubmersService
 *
 * @author Ibrahim
 */
class AddNubmersService extends AbstractNumbersService {
    public function __construct() {
        parent::__construct('add-two-integers');
        $this->setDescription('Returns a JSON string that has the sum of two integers.');
        $this->addRequestMethod('get');
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
