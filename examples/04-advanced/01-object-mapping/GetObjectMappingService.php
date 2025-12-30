<?php

require_once '../../../vendor/autoload.php';
require_once 'User.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * getObject() mapping service
 */
#[RestController('getobject', 'getObject() mapping')]
class GetObjectMappingService extends WebService {
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function create(): array {
        $user = $this->getObject(User::class);

        return [
            'message' => 'User created with getObject mapping',
            'user' => $user->toArray(),
            'method' => 'getobject_mapping'
        ];
    }
}
