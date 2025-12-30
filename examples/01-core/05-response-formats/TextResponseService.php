<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;

#[RestController('text-response', 'Plain text response service')]
class TextResponseService extends WebService {
    
    #[GetMapping]
    #[ResponseBody(contentType: 'text/plain')]
    #[AllowAnonymous]
    public function getTextData(): string {
        $data = [
            'message' => 'Hello from WebFiori HTTP',
            'timestamp' => date('Y-m-d H:i:s'),
            'format' => 'text',
            'php_version' => PHP_VERSION,
            'server_time' => time()
        ];
        
        $text = '';
        foreach ($data as $key => $value) {
            $text .= "$key: $value\n";
        }
        return $text;
    }
}
