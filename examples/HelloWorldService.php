<?php
require_once '../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\ParamType;
use WebFiori\Http\ParamOption;

class HelloWorldService extends WebService {
    public function __construct() {
        parent::__construct('hello');
        $this->setRequestMethods([RequestMethod::GET]);
        $this->setDescription('Returns a greeting message.');
        
        $this->addParameters([
            'my-name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::OPTIONAL => true,
                ParamOption::DESCRIPTION => 'Your name to include in the greeting.'
            ]
        ]);
    }
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $name = $this->getParamVal('my-name');
        
        if ($name !== null) {
            $this->sendResponse("Hello '$name'.");
        } else {
            $this->sendResponse('Hello World!');
        }
    }
}
