<?php

namespace restEasy\tests;
use webfiori\restEasy\RequestParameter;
/**
 * Description of AddNubmersService
 *
 * @author Ibrahim
 */
class AddNubmersService extends \webfiori\restEasy\WebService {
    public function __construct() {
        parent::__construct('add-two-integers');
        $this->setDescription('Returns a JSON string that has the sum of two integers.');
        $this->addRequestMethod('get');
        $this->addParameter(new RequestParameter('first-number', 'integer'));
        $this->addParameter(new RequestParameter('second-number', 'integer'));
    }
    //put your code here
    public function isAuthorized() {
        $inputs = $this->getInputs();
        $pass = isset($inputs['pass']) ? $inputs['pass'] : null;

        if ($pass == null) {
            $pass = isset($inputs['pass']) ? $inputs['pass'] : null;
        }

        if ($pass == '123') {
            return true;
        }

        return false;
    }

    public function processRequest($inputs) {
        $firstNum = $inputs['first-number'];
        $secondNumber = $inputs['second-number'];
        $sum = $firstNum+$secondNumber;
        $this->sendResponse('The sum of '.$firstNum.' and '.$secondNumber.' is '.$sum.'.');
    }

}
