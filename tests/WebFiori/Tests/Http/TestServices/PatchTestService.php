<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\RequestParameter;

class PatchTestService extends WebService {
    public function __construct() {
        parent::__construct('patch-test');
        $this->addRequestMethod('PATCH');
        $this->addParameter(new RequestParameter('field', 'string'));
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
        $inputs = $this->getInputs();
        $this->sendResponse('PATCH received', 200, 'success', $inputs);
    }
}
