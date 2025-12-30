<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * A service that demonstrates handling multiple HTTP methods for CRUD operations
 */
#[RestController('tasks', 'Task management service with CRUD operations')]
class TaskService extends WebService {
    #[PostMapping]
    #[ResponseBody(status: 201)]
    #[AllowAnonymous]
    #[RequestParam('title', 'string', false, null, 'Task title (1-100 chars)')]
    #[RequestParam('description', 'string', true, null, 'Task description (max 500 chars)')]
    public function createTask(string $title, ?string $description = null): array {
        if (!$title) {
            throw new InvalidArgumentException('Title is required for creating a task');
        }

        return [
            'id' => 1,
            'title' => $title,
            'description' => $description ?: ''
        ];
    }

    #[DeleteMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', 'int', false, null, 'Task ID to delete')]
    public function deleteTask(int $id): array {
        if (!$id) {
            throw new InvalidArgumentException('ID is required for deleting a task');
        }

        return [
            'id' => $id,
            'deleted_at' => date('Y-m-d H:i:s')
        ];
    }

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getTasks(): array {
        return [
            'tasks' => [],
            'count' => 0
        ];
    }

    #[PutMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', 'int', false, null, 'Task ID')]
    #[RequestParam('title', 'string', true, null, 'Updated task title')]
    public function updateTask(int $id, ?string $title = null): array {
        if (!$id) {
            throw new InvalidArgumentException('ID is required for updating a task');
        }

        return [
            'id' => $id,
            'title' => $title,
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
}
