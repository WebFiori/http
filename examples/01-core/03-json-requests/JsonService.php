<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

/**
 * Service demonstrating JSON request handling
 */
#[RestController('json-data', 'JSON request processing service')]
class JsonService extends WebService {
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('operation', 'string', true, 'create', 'Operation to perform')]
    public function processJsonPost(?string $operation = 'create'): array {
        return $this->processJsonRequest($operation);
    }

    #[PutMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('operation', 'string', true, 'update', 'Operation to perform')]
    public function processJsonPut(?string $operation = 'update'): array {
        return $this->processJsonRequest($operation);
    }

    private function handleCreate(Json $jsonData): array {
        $user = $jsonData->get('user');
        $preferences = $jsonData->get('preferences');

        return [
            'operation' => 'create',
            'user_data' => $user,
            'preferences' => $preferences,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    private function handleGeneric(Json $jsonData, string $operation): array {
        return [
            'operation' => $operation,
            'received_json' => $jsonData->toArray(),
            'json_keys' => array_keys($jsonData->toArray()),
            'processed_at' => date('Y-m-d H:i:s')
        ];
    }

    private function handleUpdate(Json $jsonData): array {
        $name = $jsonData->get('name');
        $email = $jsonData->get('email');

        return [
            'operation' => 'update',
            'updated_fields' => [
                'name' => $name,
                'email' => $email
            ],
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    private function handleValidate(Json $jsonData): array {
        $errors = [];

        // Validate required fields
        if (!$jsonData->hasKey('name') || empty($jsonData->get('name'))) {
            $errors[] = 'Name is required';
        }

        if (!$jsonData->hasKey('email') || !filter_var($jsonData->get('email'), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        if (!empty($errors)) {
            throw new InvalidArgumentException('Validation failed: '.implode(', ', $errors));
        }

        return ['validated_data' => $jsonData->toArray()];
    }

    private function processJsonRequest(string $operation): array {
        $inputs = $this->getInputs();

        // Check if we received JSON data
        if (!($inputs instanceof Json)) {
            throw new InvalidArgumentException('This service expects JSON data');
        }

        switch ($operation) {
            case 'create':
                return $this->handleCreate($inputs);
            case 'update':
                return $this->handleUpdate($inputs);
            case 'validate':
                return $this->handleValidate($inputs);
            default:
                return $this->handleGeneric($inputs, $operation);
        }
    }
}
