<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\Test\ServiceTestCase;
use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Json\Json;
use WebFiori\Json\JsonI;
use WebFiori\Tests\Http\TestServices\JsonIResponseService;
use WebFiori\Tests\Http\TestServices\SampleSpec;

/**
 * Tests that #[ResponseBody] serializes JsonI objects without metadata.
 *
 * @see https://github.com/WebFiori/http/issues/142
 */
class JsonISerializationTest extends ServiceTestCase {

    /**
     * Returning a JsonI object should produce clean JSON from toJSON().
     */
    public function testJsonIReturnProducesCleanJson() {
        $response = $this->get(new JsonIResponseService());
        $body = $response->getBody();
        $decoded = json_decode($body, true);

        $this->assertNotNull($decoded, 'Response should be valid JSON');
        $this->assertEquals('3.1.0', $decoded['openapi']);
        $this->assertEquals('Test API', $decoded['info']['title']);
        $this->assertEquals('1.0.0', $decoded['info']['version']);
    }

    /**
     * JsonI response should NOT contain metadata fields.
     */
    public function testJsonIResponseHasNoMetadata() {
        $response = $this->get(new JsonIResponseService());
        $body = $response->getBody();

        $this->assertStringNotContainsString('Value', $body);
        $this->assertStringNotContainsString('Case', $body);
        $this->assertStringNotContainsString('JsonXTagName', $body);
        $this->assertStringNotContainsString('Style', $body);
        $this->assertStringNotContainsString('Type', $body);
    }

    /**
     * JsonI response should NOT be wrapped in a "data" key.
     */
    public function testJsonIResponseNotWrappedInData() {
        $response = $this->get(new JsonIResponseService());
        $body = $response->getBody();
        $decoded = json_decode($body, true);

        $this->assertArrayNotHasKey('data', $decoded);
        $this->assertArrayHasKey('openapi', $decoded);
    }

    /**
     * Internal public properties of the JsonI object should not leak.
     */
    public function testInternalPropertiesNotLeaked() {
        $response = $this->get(new JsonIResponseService());
        $body = $response->getBody();

        $this->assertStringNotContainsString('internalState', $body);
        $this->assertStringNotContainsString('should-not-leak', $body);
    }

    /**
     * Returning a Json object directly should also produce clean output.
     */
    public function testJsonReturnProducesCleanOutput() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('json-direct');
            }
            public function isAuthorized(): bool { return true; }

            #[GetMapping]
            #[ResponseBody]
            #[AllowAnonymous]
            public function getData(): Json {
                $j = new Json();
                $j->add('status', 'ok');
                $j->add('count', 42);
                return $j;
            }
            public function processRequest() {}
        };

        $response = $this->get($service);
        $decoded = json_decode($response->getBody(), true);

        $this->assertEquals('ok', $decoded['status']);
        $this->assertEquals(42, $decoded['count']);
        $this->assertArrayNotHasKey('data', $decoded);
    }

    /**
     * JsonI returned with a custom content type should still serialize cleanly.
     */
    public function testJsonIWithCustomContentType() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('custom-ct');
            }
            public function isAuthorized(): bool { return true; }

            #[GetMapping]
            #[ResponseBody(contentType: 'application/vnd.oai.openapi+json')]
            #[AllowAnonymous]
            public function getSpec(): JsonI {
                return new SampleSpec();
            }
            public function processRequest() {}
        };

        $response = $this->get($service);
        $body = $response->getBody();
        $decoded = json_decode($body, true);

        $this->assertNotNull($decoded, 'Response should be valid JSON');
        $this->assertEquals('3.1.0', $decoded['openapi']);
        $this->assertArrayNotHasKey('data', $decoded);
        $this->assertStringNotContainsString('internalState', $body);
    }
}
