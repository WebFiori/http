<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * A simple user management service
 */
#[RestController('users', 'User management operations')]
class UserService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getUsers(): array {
        return [
            'users' => [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Smith']
            ]
        ];
    }
}
