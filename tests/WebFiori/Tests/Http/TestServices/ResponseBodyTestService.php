<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\WebService;

#[RestController('response-body-test')]
class ResponseBodyTestService extends WebService {
    
    // Test 1: Return array with default ResponseBody
    #[GetMapping]
    #[ResponseBody]
    public function getArrayData(): array {
        return ['users' => [['id' => 1, 'name' => 'John'], ['id' => 2, 'name' => 'Jane']]];
    }
    
    // Test 2: Return string with custom status
    #[GetMapping]
    #[ResponseBody(status: 201, type: 'created')]
    public function getStringData(): string {
        return 'Resource created successfully';
    }
    
    // Test 3: Return null (should be empty response)
    #[PostMapping]
    #[ResponseBody(status: 204)]
    public function deleteData(): null {
        // Simulate deletion
        return null;
    }
    
    // Test 4: Return object
    #[GetMapping]
    #[ResponseBody]
    public function getObjectData(): object {
        return (object)['message' => 'Hello World', 'timestamp' => time()];
    }
    
    // Test 5: Method without ResponseBody (manual handling)
    #[GetMapping]
    #[RequestParam('manual', 'string', true, 'false')]
    public function getManualData(): void {
        $manual = $this->getParamVal('manual');
        $this->sendResponse('Manual response: ' . $manual, 200, 'success');
    }
    
    // Test 6: Method with parameters and ResponseBody
    #[PostMapping]
    #[ResponseBody]
    #[RequestParam('name', 'string')]
    #[RequestParam('age', 'int', true, 25)]
    public function createUser(): array {
        return [
            'user' => [
                'name' => $this->getParamVal('name'),
                'age' => $this->getParamVal('age'),
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        // This should not be called for ResponseBody methods
        $this->sendResponse('Fallback processRequest called', 200, 'info');
    }
    
    protected function getCurrentProcessingMethod(): ?string {
        $action = $_GET['action'] ?? 'array';
        return match($action) {
            'array' => 'getArrayData',
            'string' => 'getStringData', 
            'null' => 'deleteData',
            'object' => 'getObjectData',
            'manual' => 'getManualData',
            'create' => 'createUser',
            default => null
        };
    }
}
