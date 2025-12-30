<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\RequestParameter;

class JsonBodyService extends WebService {
    public function __construct() {
        parent::__construct('json-body');
        $this->addRequestMethod('POST');
        $this->addParameter(new RequestParameter('data', 'string'));
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
        $inputs = $this->getInputs();
        $this->sendResponse('JSON received', 200, 'success', $inputs);
    }
}
