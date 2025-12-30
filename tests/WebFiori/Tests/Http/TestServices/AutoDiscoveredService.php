<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;

class AutoDiscoveredService extends WebService {
    public function __construct() {
        parent::__construct('auto-discovered');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
        $this->sendResponse('Auto-discovered service works');
    }
}
