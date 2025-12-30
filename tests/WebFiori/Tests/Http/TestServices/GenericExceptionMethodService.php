<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;

class GenericExceptionMethodService extends WebService {
    public function __construct() {
        parent::__construct('generic-exception-method');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
    }
    
    #[GetMapping]
    #[ResponseBody]
    public function throwGenericException() {
        throw new \Exception('Something went wrong');
    }
}
