<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;

class AccessDeniedTestService extends WebService {
    public function __construct() {
        parent::__construct('test-denied');
        $this->addRequestMethod('GET');
    }
    
    public function isAuthorized(): bool {
        return false;
    }
    
    public function processRequest() {
    }
}
