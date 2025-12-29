<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\WebService;

#[RestController('integration-test')]
class IntegrationTestService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    public function getData(): array {
        return ['message' => 'Auto-processing via WebServicesManager'];
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        // This should not be called when using auto-processing
        $this->sendResponse('Manual processing fallback', 200, 'info');
    }
}
