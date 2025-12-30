<?php

require_once '../../../vendor/autoload.php';
require_once 'User.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\RequestParam;

/**
 * Traditional parameter mapping service
 */
#[RestController('traditional', 'Traditional parameter mapping')]
class TraditionalMappingService extends WebService {
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', 'string', false)]
    #[RequestParam('email', 'string', false)]
    #[RequestParam('age', 'int', false)]
    public function create(string $name, string $email, int $age): array {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setAge($age);
        
        return [
            'message' => 'User created with traditional parameters',
            'user' => $user->toArray(),
            'method' => 'traditional_parameters'
        ];
    }
}
