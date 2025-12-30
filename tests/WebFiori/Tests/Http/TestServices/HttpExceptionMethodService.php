<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Exceptions\NotFoundException;

class HttpExceptionMethodService extends WebService {
    public function __construct() {
        parent::__construct('http-exception-method');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
    }
    
    #[GetMapping]
    #[ResponseBody]
    public function throwHttpException() {
        throw new NotFoundException('Resource not found');
    }
}
