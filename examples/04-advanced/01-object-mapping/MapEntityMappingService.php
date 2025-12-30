<?php

require_once '../../../vendor/autoload.php';
require_once 'User.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\MapEntity;

/**
 * MapEntity attribute mapping service
 */
#[RestController('mapentity', 'MapEntity attribute mapping')]
class MapEntityMappingService extends WebService {
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[MapEntity(User::class)]
    public function create(User $user): array {
        return [
            'message' => 'User created with MapEntity attribute',
            'user' => $user->toArray(),
            'method' => 'mapentity_basic'
        ];
    }
}
