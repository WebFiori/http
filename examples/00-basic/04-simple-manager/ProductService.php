<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;

/**
 * A simple product management service
 */
#[RestController('products', 'Product catalog operations')]
class ProductService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getProducts(): array {
        return [
            'products' => [
                ['id' => 1, 'name' => 'Laptop', 'price' => 999.99],
                ['id' => 2, 'name' => 'Mouse', 'price' => 29.99]
            ]
        ];
    }
}
