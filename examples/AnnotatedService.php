<?php
require_once '../vendor/autoload.php';

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Http\RequestMethod;

#[RestController('hello-annotated', 'A greeting service configured with annotations')]
class AnnotatedHelloService extends WebService {
    public function __construct() {
        parent::__construct(); // No need to pass name - it comes from annotation
        $this->setRequestMethods([RequestMethod::GET]);
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $this->sendResponse('Hello from annotated service!');
    }
}

// Usage example
$service = new AnnotatedHelloService();
echo "Service name: " . $service->getName() . "\n";
echo "Service description: " . $service->getDescription() . "\n";
