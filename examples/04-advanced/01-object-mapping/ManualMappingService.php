<?php

require_once '../../../vendor/autoload.php';
require_once 'User.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * Manual object mapping service
 */
#[RestController('manual', 'Manual object mapping')]
class ManualMappingService extends WebService {
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function create(): array {
        $inputs = $this->getInputs();
        $user = new User();

        if ($inputs instanceof WebFiori\Json\Json) {
            if ($inputs->hasKey('name')) {
                $user->setName($inputs->get('name'));
            }

            if ($inputs->hasKey('email')) {
                $user->setEmail($inputs->get('email'));
            }

            if ($inputs->hasKey('age')) {
                $user->setAge($inputs->get('age'));
            }
        }

        return [
            'message' => 'User created with manual mapping',
            'user' => $user->toArray(),
            'method' => 'manual_mapping'
        ];
    }
}
