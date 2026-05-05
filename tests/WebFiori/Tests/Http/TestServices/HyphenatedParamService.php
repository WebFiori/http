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

#[RestController('hyphenated-param-service')]
class HyphenatedParamService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('app-id', ParamType::INT)]
    #[RequestParam('user-name', ParamType::STRING, true)]
    public function getData(int $x, ?string $y): Json {
        $json = new Json();
        $json->add('app-id', $x);
        $json->add('user-name', $y);
        return $json;
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('first-name', ParamType::STRING)]
    #[RequestParam('last-name', ParamType::STRING)]
    #[RequestParam('email-address', ParamType::EMAIL)]
    public function createUser(string $a, string $b, string $c): Json {
        $json = new Json();
        $json->add('first-name', $a);
        $json->add('last-name', $b);
        $json->add('email-address', $c);
        return $json;
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
