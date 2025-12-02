<?php
require_once '../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\ParamType;
use WebFiori\Http\ParamOption;

class GetRandomService extends WebService {
    public function __construct() {
        parent::__construct('get-random');
        $this->setRequestMethods([RequestMethod::GET, RequestMethod::POST]);
        $this->setDescription('Returns a random integer. If no range is specified, the method will return a number between 0 and getrandmax().');
        
        $this->addParameters([
            'min' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true,
                ParamOption::DESCRIPTION => 'Minimum value for the random number.'
            ],
            'max' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true,
                ParamOption::DESCRIPTION => 'Maximum value for the random number.'
            ]
        ]);
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $max = $this->getParamVal('max');
        $min = $this->getParamVal('min');

        if ($max !== null && $min !== null) {
            $random = rand($min, $max);
        } else {
            $random = rand();
        }
        
        $this->sendResponse('Random number generated', 'success', 200, [
            'number' => $random
        ]);
    }
}
