<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * A JsonI object used for testing direct serialization.
 */
class SampleSpec implements JsonI {
    public string $internalState = 'should-not-leak';

    public function toJSON(): Json {
        $j = new Json();
        $j->add('openapi', '3.1.0');
        $info = new Json();
        $info->add('title', 'Test API');
        $info->add('version', '1.0.0');
        $j->add('info', $info);
        $j->add('paths', new Json());
        return $j;
    }
}

/**
 * Test service that returns JsonI objects from #[ResponseBody] methods.
 */
#[RestController('jsoni-service')]
class JsonIResponseService extends WebService {
    public function isAuthorized(): bool {
        return true;
    }

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getSpec(): JsonI {
        return new SampleSpec();
    }

    public function processRequest() {
    }
}
