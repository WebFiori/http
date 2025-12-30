<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * Service demonstrating comprehensive error handling
 */
#[RestController('error-demo', 'Error handling demonstration service')]
class ErrorService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('operation', 'string', false, null, 'Operation to perform')]
    #[RequestParam('age', 'int', true, null, 'Age for validation testing (0-150)')]
    #[RequestParam('a', 'double', true, null, 'First number for division')]
    #[RequestParam('b', 'double', true, null, 'Second number for division')]
    public function handleAction(string $operation, ?int $age = null, ?float $a = null, ?float $b = null): array {
        switch ($operation) {
            case 'success':
                return $this->handleSuccess();
            case 'validate':
                return $this->handleValidation($age);
            case 'divide':
                return $this->handleDivision($a, $b);
            case 'not-found':
                throw new Exception('The requested resource was not found', 404);
            case 'server-error':
                throw new Exception('Simulated server error for testing purposes', 500);
            case 'unauthorized':
                throw new Exception('Access denied: insufficient permissions', 403);
            default:
                throw new InvalidArgumentException("Unknown operation: $operation. Available: success, validate, divide, not-found, server-error, unauthorized");
        }
    }

    private function handleDivision(?float $a, ?float $b): array {
        if ($a === null || $b === null) {
            throw new InvalidArgumentException('Both parameters a and b are required for division');
        }

        if ($b == 0) {
            throw new InvalidArgumentException('Division by zero is not allowed');
        }

        return [
            'operands' => ['a' => $a, 'b' => $b],
            'result' => $a / $b
        ];
    }

    private function handleSuccess(): array {
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'OK'
        ];
    }

    private function handleValidation(?int $age): array {
        if ($age === null) {
            throw new InvalidArgumentException('Age parameter is required for validation test');
        }

        if ($age < 18) {
            throw new InvalidArgumentException("Age must be 18 or older. Provided: $age");
        }

        return ['validated_age' => $age];
    }
}
