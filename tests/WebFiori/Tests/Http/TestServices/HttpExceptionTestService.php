<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\HttpException;

class HttpExceptionTestService extends WebService {
    public function __construct() {
        parent::__construct('test-http-exception');
        $this->addRequestMethod('GET');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
        throw new HttpException('Not found', 404);
    }
}
