<?php

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\WebService;
use WebFiori\Http\WebServicesManager;

#[RestController('products', 'Product management API')]
class ProductController extends WebService {
    
    #[GetMapping]
    #[RequestParam('page', 'int', true, 1, 'Page number')]
    #[RequestParam('limit', 'int', true, 10, 'Items per page')]
    public function getProducts() {
        $page = $this->getParamVal('page');
        $limit = $this->getParamVal('limit');
        
        $this->sendResponse('Products retrieved', 'success', 200, [
            'page' => $page,
            'limit' => $limit,
            'products' => []
        ]);
    }
    
    #[PostMapping]
    #[RequestParam('name', 'string', false, null, 'Product name')]
    #[RequestParam('price', 'float', false, null, 'Product price')]
    #[RequestParam('category', 'string', true, 'General', 'Product category')]
    public function createProduct() {
        $name = $this->getParamVal('name');
        $price = $this->getParamVal('price');
        $category = $this->getParamVal('category');
        
        $this->sendResponse('Product created', 'success', 201, [
            'id' => 123,
            'name' => $name,
            'price' => $price,
            'category' => $category
        ]);
    }
    
    #[PutMapping]
    #[RequestParam('id', 'int', false, null, 'Product ID')]
    #[RequestParam('name', 'string', true)]
    #[RequestParam('price', 'float', true)]
    public function updateProduct() {
        $id = $this->getParamVal('id');
        $name = $this->getParamVal('name');
        $price = $this->getParamVal('price');
        
        $this->sendResponse('Product updated', 'success', 200, [
            'id' => $id,
            'name' => $name,
            'price' => $price
        ]);
    }
    
    #[DeleteMapping]
    #[RequestParam('id', 'int', false, null, 'Product ID to delete')]
    public function deleteProduct() {
        $id = $this->getParamVal('id');
        $this->sendResponse('Product deleted', 'success', 200, ['id' => $id]);
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        switch ($method) {
            case \WebFiori\Http\RequestMethod::GET:
                $this->getProducts();
                break;
            case \WebFiori\Http\RequestMethod::POST:
                $this->createProduct();
                break;
            case \WebFiori\Http\RequestMethod::PUT:
                $this->updateProduct();
                break;
            case \WebFiori\Http\RequestMethod::DELETE:
                $this->deleteProduct();
                break;
        }
    }
}