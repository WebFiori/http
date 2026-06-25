<?php

require_once '../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

#[RestController('items', 'Item management service')]
#[AllowAnonymous]
class ItemService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[RequestParam('id', ParamType::INT, true)]
    public function getItem(?int $id): array {
        if ($id === null) {
            return ['items' => [
                ['id' => 1, 'name' => 'Widget'],
                ['id' => 2, 'name' => 'Gadget'],
            ]];
        }

        if ($id > 2) {
            return ['error' => 'Item not found'];
        }

        return ['id' => $id, 'name' => 'Widget'];
    }

    #[PostMapping]
    #[ResponseBody]
    #[RequestParam('name', ParamType::STRING)]
    #[RequestParam('price', ParamType::DOUBLE)]
    public function createItem(string $name, float $price): array {
        return [
            'id' => 3,
            'name' => $name,
            'price' => $price,
        ];
    }
}
