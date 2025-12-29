<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\WebService;

#[RestController('all-methods')]
class AllMethodsService extends WebService {
    
    #[GetMapping]
    public function getData() {}
    
    #[PostMapping]
    public function createData() {}
    
    #[PutMapping]
    public function updateData() {}
    
    #[DeleteMapping]
    public function deleteData() {}
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $this->sendResponse('All methods service');
    }
}
