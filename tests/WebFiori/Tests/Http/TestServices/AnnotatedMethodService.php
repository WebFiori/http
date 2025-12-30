<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\Param;

class AnnotatedMethodService extends WebService {
    public function __construct() {
        parent::__construct('annotated-method');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
    }
    
    #[GetMapping]
    #[ResponseBody]
    #[Param(name: 'id', type: 'integer')]
    public function getItem() {
        return ['id' => $_GET['id'] ?? 0, 'name' => 'Test Item'];
    }
}
