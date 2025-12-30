<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\RequiresAuth;

class ConflictingAnnotationsService extends WebService {
    public function __construct() {
        parent::__construct('conflicting');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
    }
    
    #[GetMapping]
    #[AllowAnonymous]
    #[RequiresAuth]
    public function conflictingMethod() {
        return ['data' => 'test'];
    }
}
