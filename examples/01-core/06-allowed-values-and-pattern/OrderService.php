<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

/**
 * Service demonstrating allowed-values and pattern validation.
 */
#[RestController('orders', 'Order management with enum and pattern validation')]
class OrderService extends WebService {

    /**
     * Get orders filtered by status.
     * The status parameter only accepts specific values.
     */
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('status', ParamType::STRING, allowedValues: ['pending', 'shipped', 'delivered', 'cancelled'])]
    #[RequestParam('sort', ParamType::STRING, true, 'date', allowedValues: ['date', 'total', 'status'])]
    public function getOrders(string $status, string $sort = 'date'): array {
        return [
            'filters' => [
                'status' => $status,
                'sort' => $sort,
            ],
            'orders' => [
                ['id' => 1, 'status' => $status, 'total' => 29.99],
                ['id' => 2, 'status' => $status, 'total' => 59.99],
            ]
        ];
    }

    /**
     * Create a new order.
     * Phone must match international format, postal code must be 5 digits.
     */
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('customer_name', ParamType::STRING)]
    #[RequestParam('phone', ParamType::STRING, pattern: '/^\+[0-9]{10,15}$/')]
    #[RequestParam('postal_code', ParamType::STRING, pattern: '/^[0-9]{5}$/')]
    #[RequestParam('country', ParamType::STRING, allowedValues: ['US', 'CA', 'UK', 'DE', 'FR'])]
    public function createOrder(string $name, string $phone, string $postalCode, string $country): array {
        return [
            'message' => 'Order created',
            'customer' => [
                'name' => $name,
                'phone' => $phone,
                'postal_code' => $postalCode,
                'country' => $country,
            ]
        ];
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
