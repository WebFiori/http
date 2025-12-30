<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;

class ResponseBodyService extends WebService {
    public function __construct() {
        parent::__construct('response-body');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
    }
    
    #[PostMapping]
    #[ResponseBody(status: 201, type: 'success')]
    public function createResource() {
        return ['id' => 123, 'name' => 'New Resource'];
    }
    
    #[GetMapping]
    #[ResponseBody(contentType: 'text/plain')]
    public function getPlainText() {
        return 'Plain text response';
    }
}
