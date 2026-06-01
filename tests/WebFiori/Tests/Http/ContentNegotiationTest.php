<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\Produces;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ErrorResponse;
use WebFiori\Http\MediaType;
use WebFiori\Http\ResponseEntity;
use WebFiori\Http\Test\ServiceTestCase;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;
use WebFiori\Tests\Http\TestServices\ContentNegotiationService;

/**
 * Tests for content negotiation with #[Produces] attribute.
 */
class ContentNegotiationTest extends ServiceTestCase {

    // =========================================================================
    // No #[Produces] — default behavior unchanged
    // =========================================================================

    public function testNoProducesNoAcceptReturnsJson() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('no-produces');
                $this->addRequestMethod('GET');
            }
            #[GetMapping]
            #[ResponseBody]
            #[AllowAnonymous]
            public function getData(): array {
                return ['format' => 'json'];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->get($service)
            ->assertOk()
            ->assertJson();
    }

    public function testNoProducesWithAcceptXmlStillReturnsJson() {
        // Without #[Produces], no negotiation happens — always JSON
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('no-produces-xml');
                $this->addRequestMethod('GET');
            }
            #[GetMapping]
            #[ResponseBody]
            #[AllowAnonymous]
            public function getData(): array {
                return ['format' => 'json'];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->get($service, [], null, ['accept' => 'application/xml'])
            ->assertOk()
            ->assertJson();
    }

    // =========================================================================
    // #[Produces] with matching Accept
    // =========================================================================

    public function testProducesJsonAcceptJsonWorks() {
        $this->get(new ContentNegotiationService(), ['id' => '1'], null, ['accept' => 'application/json'])
            ->assertOk()
            ->assertJson()
            ->assertBodyContains('John');
    }

    public function testProducesXmlAcceptXmlWorks() {
        $response = $this->get(new ContentNegotiationService(), ['id' => '1'], null, ['accept' => 'application/xml']);
        $this->assertStringContainsString('<user>', $response->getBody());
        $this->assertStringContainsString('<name>John</name>', $response->getBody());
    }

    // =========================================================================
    // #[Produces] with no match → 406
    // =========================================================================

    public function testProducesJsonXmlAcceptHtmlReturns406() {
        $this->get(new ContentNegotiationService(), ['id' => '1'], null, ['accept' => 'text/html'])
            ->assertStatus(406)
            ->assertBodyContains('Not Acceptable');
    }

    public function testProducesAcceptPlainTextReturns406() {
        $this->get(new ContentNegotiationService(), ['id' => '1'], null, ['accept' => 'text/plain'])
            ->assertStatus(406);
    }

    // =========================================================================
    // Wildcard and priority
    // =========================================================================

    public function testWildcardAcceptUsesServerDefault() {
        $this->get(new ContentNegotiationService(), ['id' => '1'], null, ['accept' => '*/*'])
            ->assertOk()
            ->assertJson();
    }

    public function testApplicationWildcardWorks() {
        $this->get(new ContentNegotiationService(), ['id' => '1'], null, ['accept' => 'application/*'])
            ->assertOk();
    }

    public function testQValuePriorityRespected() {
        // XML has higher priority (q=1.0) than JSON (q=0.5)
        $response = $this->get(new ContentNegotiationService(), ['id' => '1'], null, [
            'accept' => 'application/json;q=0.5, application/xml;q=1.0'
        ]);
        $this->assertStringContainsString('<user>', $response->getBody());
    }

    public function testQValueJsonHigherReturnsJson() {
        $this->get(new ContentNegotiationService(), ['id' => '1'], null, [
            'accept' => 'application/json;q=1.0, application/xml;q=0.5'
        ])
            ->assertOk()
            ->assertJson();
    }

    // =========================================================================
    // No Accept header
    // =========================================================================

    public function testNoAcceptHeaderUsesServerDefault() {
        $this->get(new ContentNegotiationService(), ['id' => '1'])
            ->assertOk()
            ->assertJson();
    }

    // =========================================================================
    // getNegotiatedContentType()
    // =========================================================================

    public function testGetNegotiatedContentTypeReturnsXml() {
        $response = $this->get(new ContentNegotiationService(), ['id' => '1'], null, ['accept' => 'application/xml']);
        // The service uses getNegotiatedContentType() to decide format
        $this->assertStringContainsString('<?xml', $response->getBody());
    }

    // =========================================================================
    // ErrorResponse::notAcceptable
    // =========================================================================

    public function testNotAcceptableResponse() {
        $result = ErrorResponse::notAcceptable([MediaType::JSON, MediaType::XML]);
        $this->assertEquals(406, $result['code']);
        $json = $result['json'];
        $this->assertEquals('Not Acceptable', $json->get('message'));
        $this->assertEquals(406, $json->get('http-code'));
    }

    // =========================================================================
    // MediaType constants
    // =========================================================================

    public function testMediaTypeConstants() {
        $this->assertEquals('application/json', MediaType::JSON);
        $this->assertEquals('application/xml', MediaType::XML);
        $this->assertEquals('text/html', MediaType::HTML);
        $this->assertEquals('text/plain', MediaType::PLAIN);
    }
}
