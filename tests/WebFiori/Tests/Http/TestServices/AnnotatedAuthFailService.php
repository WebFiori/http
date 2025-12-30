<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;

class AnnotatedAuthFailService extends WebService {
    public function __construct() {
        parent::__construct('annotated-auth-fail');
    }
    
    public function isAuthorized(): bool {
        return false;
    }
    
    public function processRequest() {
    }
    
    #[GetMapping]
    #[ResponseBody]
    public function getData() {
        return ['data' => 'test'];
    }
}
