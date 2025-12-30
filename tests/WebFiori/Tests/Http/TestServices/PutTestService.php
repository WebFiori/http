<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\RequestParameter;

class PutTestService extends WebService {
    public function __construct() {
        parent::__construct('put-test');
        $this->addRequestMethod('PUT');
        $this->addParameter(new RequestParameter('name', 'string'));
        $this->addParameter(new RequestParameter('value', 'integer'));
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
        $inputs = $this->getInputs();
        $this->sendResponse('PUT received', 200, 'success', $inputs);
    }
}
