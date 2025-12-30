<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;

class SimpleExceptionService extends WebService {
    public function __construct() {
        parent::__construct('test-simple-exception');
        $this->addRequestMethod('GET');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
        throw new \Exception('Test exception');
    }
}
