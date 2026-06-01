<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

#[RestController('allowed-values-pattern-service')]
class AllowedValuesPatternService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('status', ParamType::STRING, allowedValues: ['active', 'inactive', 'pending'])]
    public function getByStatus(string $status): Json {
        $json = new Json();
        $json->add('status', $status);
        return $json;
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('phone', ParamType::STRING, pattern: '/^\+[0-9]{10,15}$/')]
    public function createWithPhone(string $phone): Json {
        $json = new Json();
        $json->add('phone', $phone);
        return $json;
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
