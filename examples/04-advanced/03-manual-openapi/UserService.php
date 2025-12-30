<?php

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\Param;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\ParamType;

#[RestController('users', 'User management operations')]
class UserService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Param('id', ParamType::INT, 'User ID', min: 1)]
    public function getUser(?int $id): array {
        return [
            'id' => $id ?? 1,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];
    }
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Param('name', ParamType::STRING, 'User full name', minLength: 2, maxLength: 100)]
    #[Param('email', ParamType::EMAIL, 'User email address')]
    public function createUser(string $name, string $email): array {
        return [
            'id' => 2,
            'name' => $name,
            'email' => $email
        ];
    }
}
