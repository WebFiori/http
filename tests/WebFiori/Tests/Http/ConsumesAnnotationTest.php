<?php

namespace WebFiori\Tests\Http;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\Consumes;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\MediaType;
use WebFiori\Http\Test\ServiceTestCase;
use WebFiori\Http\WebService;

/**
 * Tests for the #[Consumes] annotation for per-method content type control.
 */
class ConsumesAnnotationTest extends ServiceTestCase {

    // =========================================================================
    // No #[Consumes] — default behavior unchanged
    // =========================================================================

    public function testNoConsumesDefaultBehaviorStillWorks() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('no-consumes-default');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[ResponseBody]
            #[AllowAnonymous]
            public function createItem(): array {
                return ['created' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->post($service, ['name' => 'test'])
            ->assertOk()
            ->assertJson();
    }

    public function testNoConsumesRejectsUnsupportedType() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('no-consumes-reject');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[ResponseBody]
            #[AllowAnonymous]
            public function createItem(): array {
                return ['created' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->post($service, [], null, ['content-type' => 'application/octet-stream'])
            ->assertStatus(415);
    }

    // =========================================================================
    // #[Consumes] present — custom types allowed
    // =========================================================================

    public function testConsumesAllowsListedType() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-octet');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[Consumes(MediaType::OCTET_STREAM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function uploadFile(): array {
                return ['uploaded' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->post($service, [], null, ['content-type' => 'application/octet-stream'])
            ->assertOk()
            ->assertJson();
    }

    public function testConsumesRejectsUnlistedType() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-reject');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[Consumes(MediaType::OCTET_STREAM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function uploadFile(): array {
                return ['uploaded' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->post($service, [], null, ['content-type' => 'text/csv'])
            ->assertStatus(415);
    }

    public function testConsumesSkipsParameterParsing() {
        // When using a non-parseable type, parameters should NOT be filtered.
        // The service should still be dispatched and can read raw body.
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-no-parse');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[Consumes(MediaType::OCTET_STREAM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function uploadFile(): array {
                // If parameter parsing was skipped, getParamVal should return null
                return ['param_value' => $this->getParamVal('name')];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        // Even though we pass 'name' param, it shouldn't be filtered because
        // octet-stream is not a parseable type
        $this->post($service, ['name' => 'test'], null, ['content-type' => 'application/octet-stream'])
            ->assertOk();
    }

    public function testConsumesMultipleTypes() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-multi');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[Consumes(MediaType::XML, 'text/xml')]
            #[ResponseBody]
            #[AllowAnonymous]
            public function acceptXml(): array {
                return ['accepted' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        // application/xml should work
        $this->post($service, [], null, ['content-type' => 'application/xml'])
            ->assertOk();

        // text/xml should also work
        $this->post($service, [], null, ['content-type' => 'text/xml'])
            ->assertOk();

        // text/csv should be rejected
        $this->post($service, [], null, ['content-type' => 'text/csv'])
            ->assertStatus(415);
    }

    public function testConsumesWithStandardTypeStillFilters() {
        // If #[Consumes] includes form-urlencoded, normal parameter filtering should occur
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-form');
                $this->addRequestMethod('POST');
                $this->addParameter([
                    'name' => 'username',
                    'type' => 'string',
                    'optional' => false,
                ]);
            }
            #[PostMapping]
            #[Consumes(MediaType::FORM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function createUser(): array {
                return ['user' => $this->getParamVal('username')];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        // Missing required param should result in an error response (422 = validation error)
        $this->post($service, [], null, ['content-type' => 'application/x-www-form-urlencoded'])
            ->assertStatus(422);
    }

    public function testConsumesOnGetMethodIsIgnored() {
        // GET requests bypass content type checks regardless of annotation
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-get');
                $this->addRequestMethod('GET');
            }
            #[GetMapping]
            #[Consumes(MediaType::OCTET_STREAM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function getData(): array {
                return ['data' => 'hello'];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->get($service)
            ->assertOk()
            ->assertJson();
    }

    public function testConsumesWithPutMethod() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-put');
                $this->addRequestMethod('PUT');
            }
            #[PutMapping]
            #[Consumes(MediaType::OCTET_STREAM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function replaceFile(): array {
                return ['replaced' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->put($service, [], null, ['content-type' => 'application/octet-stream'])
            ->assertOk()
            ->assertJson();
    }

    public function testConsumesIntegrationWithResponseBody() {
        // Full pipeline: Consumes + ResponseBody on same method
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-full');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[Consumes(MediaType::OCTET_STREAM, MediaType::FORM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function upload(): array {
                return ['status' => 'received'];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        // octet-stream should work
        $this->post($service, [], null, ['content-type' => 'application/octet-stream'])
            ->assertOk()
            ->assertJson();

        // form-urlencoded should also work
        $this->post($service, ['foo' => 'bar'], null, ['content-type' => 'application/x-www-form-urlencoded'])
            ->assertOk()
            ->assertJson();
    }

    public function testConsumesWithContentTypeCharset() {
        // Content-Type: application/x-www-form-urlencoded; charset=utf-8 should still match
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-charset');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[Consumes(MediaType::FORM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function acceptForm(): array {
                return ['ok' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->post($service, ['x' => '1'], null, ['content-type' => 'application/x-www-form-urlencoded; charset=utf-8'])
            ->assertOk();
    }

    public function testConsumesOverridesDefaultTypes() {
        // If #[Consumes] only lists octet-stream, the default types (form, json)
        // should be REJECTED
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('consumes-override');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[Consumes(MediaType::OCTET_STREAM)]
            #[ResponseBody]
            #[AllowAnonymous]
            public function binaryOnly(): array {
                return ['binary' => true];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        // form-urlencoded should be rejected because #[Consumes] overrides defaults
        $this->post($service, ['foo' => 'bar'])
            ->assertStatus(415);
    }
}
