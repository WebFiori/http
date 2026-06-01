<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\UseParameterSet;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

#[RestController('parameter-set-service')]
class ParameterSetService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[UseParameterSet(PaginationParams::class)]
    public function listItems(int $page = 1, int $perPage = 20): Json {
        $json = new Json();
        $json->add('page', $page);
        $json->add('per_page', $perPage);
        return $json;
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[UseParameterSet(AddressParams::class)]
    #[RequestParam('note', ParamType::STRING, true)]
    public function createWithAddress(string $street, string $city, string $zip, string $country, ?string $note): Json {
        $json = new Json();
        $json->add('street', $street);
        $json->add('city', $city);
        $json->add('zip', $zip);
        $json->add('country', $country);
        $json->add('note', $note);
        return $json;
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
