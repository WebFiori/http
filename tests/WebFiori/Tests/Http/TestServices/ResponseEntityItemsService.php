<?php
namespace WebFiori\Tests\Http\TestServices;

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

#[RestController('response-entity-items')]
class ResponseEntityItemsService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', ParamType::INT)]
    public function getItem(int $id): ResponseEntity {
        if ($id === 999) {
            return ResponseEntity::notFound(new Json(['message' => 'Item not found']));
        }
        if ($id === 0) {
            return ResponseEntity::badRequest(new Json(['message' => 'Invalid ID']));
        }
        return ResponseEntity::ok(new Json(['id' => $id, 'name' => 'Item ' . $id]));
    }

    #[DeleteMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', ParamType::INT)]
    public function deleteItem(int $id): ResponseEntity {
        if ($id === 999) {
            return ResponseEntity::notFound(new Json(['message' => 'Item not found']));
        }
        return ResponseEntity::noContent();
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', ParamType::STRING)]
    public function createItem(string $name): ResponseEntity {
        return ResponseEntity::created(['id' => 42, 'name' => $name]);
    }

    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}
