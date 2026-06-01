<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\UseParameterSet;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\ParameterSet;
use WebFiori\Http\RequestProcessor;
use WebFiori\Http\WebService;

// Define reusable parameter sets

class PaginationParams implements ParameterSet {
    public function getParameters(): array {
        return [
            'page' => [ParamOption::TYPE => ParamType::INT, ParamOption::OPTIONAL => true, ParamOption::DEFAULT => 1, ParamOption::MIN => 1],
            'per_page' => [ParamOption::TYPE => ParamType::INT, ParamOption::OPTIONAL => true, ParamOption::DEFAULT => 20, ParamOption::MIN => 1, ParamOption::MAX => 100],
        ];
    }
}

class AddressParams implements ParameterSet {
    public function getParameters(): array {
        return [
            'street' => [ParamOption::TYPE => ParamType::STRING],
            'city' => [ParamOption::TYPE => ParamType::STRING],
            'zip' => [ParamOption::TYPE => ParamType::STRING, ParamOption::PATTERN => '/^[0-9]{5}$/'],
            'country' => [ParamOption::TYPE => ParamType::STRING, ParamOption::ALLOWED_VALUES => ['US', 'UK', 'DE']],
        ];
    }
}

// Use parameter sets in services via attributes

#[RestController('orders')]
class OrderService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[UseParameterSet(PaginationParams::class)]
    public function listOrders(int $page = 1, int $perPage = 20): array {
        return [
            'page' => $page,
            'per_page' => $perPage,
            'orders' => [
                ['id' => 1, 'total' => 29.99],
                ['id' => 2, 'total' => 59.99],
            ]
        ];
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[UseParameterSet(AddressParams::class)]
    #[RequestParam('total', ParamType::DOUBLE)]
    public function createOrder(string $street, string $city, string $zip, string $country, float $total): array {
        return [
            'message' => 'Order created',
            'address' => compact('street', 'city', 'zip', 'country'),
            'total' => $total,
        ];
    }

    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}

$processor = new RequestProcessor();
$processor->process(new OrderService());
