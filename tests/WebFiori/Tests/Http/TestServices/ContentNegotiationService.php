<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\Produces;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\MediaType;
use WebFiori\Http\ParamType;
use WebFiori\Http\ResponseEntity;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

#[RestController('content-negotiation-service')]
class ContentNegotiationService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Produces(MediaType::JSON, MediaType::XML)]
    #[RequestParam('id', ParamType::INT, true, 1)]
    public function getUser(int $id = 1): ResponseEntity {
        $negotiated = $this->getNegotiatedContentType();

        if ($negotiated === MediaType::XML) {
            $xml = "<?xml version=\"1.0\"?><user><id>$id</id><name>John</name></user>";
            return new ResponseEntity($xml, 200, MediaType::XML);
        }

        return ResponseEntity::ok(new Json(['id' => $id, 'name' => 'John']));
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
