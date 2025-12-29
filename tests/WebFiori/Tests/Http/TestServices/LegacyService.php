<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\WebService;

#[RestController('legacy-service')]
class LegacyService extends WebService {
    
    // Traditional service without any ResponseBody annotations
    #[GetMapping]
    public function getData(): void {
        $this->sendResponse('Legacy service response', 200, 'success', ['legacy' => true]);
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $this->getData();
    }
}
