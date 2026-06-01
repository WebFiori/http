<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

#[RestController('email-param-injection')]
class EmailParamInjectionService extends WebService {

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('email', ParamType::EMAIL)]
    public function updateUserInfo(string $email): Json {
        $json = new Json();
        $json->add('email', $email);
        return $json;
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
