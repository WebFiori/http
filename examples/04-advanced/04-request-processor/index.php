<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestProcessor;
use WebFiori\Http\WebService;

#[RestController('greet')]
class GreetService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', ParamType::STRING, true)]
    public function hello(?string $name): array {
        return ['message' => 'Hello, ' . ($name ?? 'World') . '!'];
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('to', ParamType::STRING)]
    #[RequestParam('body', ParamType::STRING)]
    public function sendGreeting(string $to, string $body): array {
        return ['sent_to' => $to, 'body' => $body, 'timestamp' => time()];
    }

    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}

// Process directly — no WebServicesManager needed
$processor = new RequestProcessor();
$processor->process(new GreetService());
