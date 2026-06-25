<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\ResponseEntity;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

/**
 * A task management service demonstrating:
 * - Positional parameter injection with hyphenated names
 * - Dynamic HTTP status codes via ResponseEntity
 */
#[RestController('tasks', 'Task management service')]
#[AllowAnonymous]
class TaskService extends WebService {

    private array $tasks = [
        1 => ['id' => 1, 'name' => 'Write documentation', 'priority' => 'high'],
        2 => ['id' => 2, 'name' => 'Fix bugs', 'priority' => 'medium'],
        3 => ['id' => 3, 'name' => 'Add tests', 'priority' => 'low'],
    ];

    #[GetMapping]
    #[ResponseBody]
    #[RequestParam('task-id', ParamType::INT, true)]
    public function getTask(?int $id): ResponseEntity {
        if ($id === null) {
            return ResponseEntity::ok(new Json(['tasks' => array_values($this->tasks)]));
        }

        if (!isset($this->tasks[$id])) {
            return ResponseEntity::notFound(new Json(['message' => "Task $id not found"]));
        }

        return ResponseEntity::ok(new Json($this->tasks[$id]));
    }

    #[PostMapping]
    #[ResponseBody]
    #[RequestParam('task-name', ParamType::STRING)]
    #[RequestParam('task-priority', ParamType::STRING, true)]
    public function createTask(string $name, ?string $priority): ResponseEntity {
        $newId = max(array_keys($this->tasks)) + 1;
        $task = [
            'id' => $newId,
            'name' => $name,
            'priority' => $priority ?? 'medium',
        ];

        return ResponseEntity::created(new Json($task));
    }

    #[DeleteMapping]
    #[ResponseBody]
    #[RequestParam('task-id', ParamType::INT)]
    public function deleteTask(int $id): ResponseEntity {
        if (!isset($this->tasks[$id])) {
            return ResponseEntity::notFound(new Json(['message' => "Task $id not found"]));
        }

        return ResponseEntity::noContent();
    }
}
