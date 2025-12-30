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
 * MapEntity with custom setters mapping service
 */
#[RestController('mapentity-custom', 'MapEntity with custom setters')]
class MapEntityCustomMappingService extends WebService {
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[MapEntity(User::class, setters: ['full-name' => 'setFullName', 'email-address' => 'setEmailAddress', 'user-age' => 'setUserAge'])]
    public function create(User $user): array {
        return [
            'message' => 'User created with MapEntity + custom setters',
            'user' => $user->toArray(),
            'method' => 'mapentity_custom'
        ];
    }
}
