<?php

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\Param;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\ParamType;

#[RestController('products', 'Product catalog management')]
class ProductService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Param('category', ParamType::STRING, 'Product category')]
    public function listProducts(?string $category): array {
        return [
            ['id' => 1, 'name' => 'Product A', 'category' => $category ?? 'Electronics'],
            ['id' => 2, 'name' => 'Product B', 'category' => $category ?? 'Electronics']
        ];
    }
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Param('name', ParamType::STRING, 'Product name')]
    #[Param('price', ParamType::DOUBLE, 'Product price', min: 0)]
    public function createProduct(string $name, float $price): array {
        return ['id' => 3, 'name' => $name, 'price' => $price];
    }
    
    #[PutMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Param('id', ParamType::INT, 'Product ID')]
    #[Param('name', ParamType::STRING, 'Product name')]
    public function updateProduct(int $id, string $name): array {
        return ['id' => $id, 'name' => $name];
    }
    
    #[DeleteMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Param('id', ParamType::INT, 'Product ID')]
    public function deleteProduct(int $id): array {
        return ['deleted' => $id];
    }
}
