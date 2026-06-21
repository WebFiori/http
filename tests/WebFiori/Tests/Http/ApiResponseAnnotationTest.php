<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\OpenAPI\ResponsesObj;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\ApiResponseAnnotatedService;

/**
 * Tests for #[ApiResponse] annotation support in OpenAPI spec generation.
 *
 * @see https://github.com/WebFiori/http/issues/143
 */
class ApiResponseAnnotationTest extends TestCase {

    /**
     * Test that #[ApiResponse] annotations on a GET method produce the correct responses in the spec.
     */
    public function testGetMethodApiResponses() {
        $service = new ApiResponseAnnotatedService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $responses = $json['get']['responses'];
        $this->assertArrayHasKey('200', $responses);
        $this->assertArrayHasKey('404', $responses);
        $this->assertEquals('List of products', $responses['200']['description']);
        $this->assertEquals('Product not found', $responses['404']['description']);
    }

    /**
     * Test that #[ApiResponse] annotations on a POST method produce multiple responses.
     */
    public function testPostMethodMultipleApiResponses() {
        $service = new ApiResponseAnnotatedService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $responses = $json['post']['responses'];
        $this->assertArrayHasKey('201', $responses);
        $this->assertArrayHasKey('400', $responses);
        $this->assertArrayHasKey('409', $responses);
        $this->assertEquals('Product created successfully', $responses['201']['description']);
        $this->assertEquals('Invalid input data', $responses['400']['description']);
        $this->assertEquals('Product already exists', $responses['409']['description']);
    }

    /**
     * Test that methods without #[ApiResponse] fall back to default "200 - Successful operation".
     */
    public function testMethodWithoutApiResponseUsesDefault() {
        $service = new ApiResponseAnnotatedService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $responses = $json['delete']['responses'];
        $this->assertArrayHasKey('200', $responses);
        $this->assertEquals('Successful operation', $responses['200']['description']);
        $this->assertArrayNotHasKey('404', $responses);
    }

    /**
     * Test that programmatic addResponse() takes priority over #[ApiResponse] annotations.
     */
    public function testProgrammaticResponseTakesPriority() {
        $service = new ApiResponseAnnotatedService();
        // Programmatically override GET responses
        $service->addResponse(RequestMethod::GET, '200', 'Overridden response');
        $service->addResponse(RequestMethod::GET, '500', 'Server error');

        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $responses = $json['get']['responses'];
        $this->assertEquals('Overridden response', $responses['200']['description']);
        $this->assertEquals('Server error', $responses['500']['description']);
        // The annotated 404 should NOT appear since programmatic takes priority
        $this->assertArrayNotHasKey('404', $responses);
    }

    /**
     * Test that #[ApiResponse] annotations are included when service is registered in a manager.
     */
    public function testApiResponseInManagerOpenAPI() {
        $manager = new WebServicesManager();
        $manager->addService(new ApiResponseAnnotatedService());
        $openapi = $manager->toOpenAPI();
        $json = json_decode($openapi->toJSON() . '', true);

        $path = $json['paths']['/api-response-service'];
        $this->assertArrayHasKey('200', $path['get']['responses']);
        $this->assertEquals('List of products', $path['get']['responses']['200']['description']);
    }

    /**
     * Test that a method with a single #[ApiResponse] generates exactly one response entry.
     */
    public function testSingleApiResponse() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('single-response');
            }

            public function isAuthorized(): bool {
                return true;
            }

            #[\WebFiori\Http\Annotations\GetMapping]
            #[\WebFiori\Http\Annotations\ApiResponse(status: '204', description: 'No content')]
            public function noContent() {
            }

            public function processRequest() {
            }
        };

        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $responses = $json['get']['responses'];
        $this->assertCount(1, $responses);
        $this->assertArrayHasKey('204', $responses);
        $this->assertEquals('No content', $responses['204']['description']);
    }

    /**
     * Test that #[ApiResponse] works with status code ranges (e.g., "2XX").
     */
    public function testApiResponseWithStatusRange() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('range-response');
            }

            public function isAuthorized(): bool {
                return true;
            }

            #[\WebFiori\Http\Annotations\GetMapping]
            #[\WebFiori\Http\Annotations\ApiResponse(status: '2XX', description: 'Successful')]
            #[\WebFiori\Http\Annotations\ApiResponse(status: '4XX', description: 'Client error')]
            public function rangeResponse() {
            }

            public function processRequest() {
            }
        };

        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $responses = $json['get']['responses'];
        $this->assertArrayHasKey('2XX', $responses);
        $this->assertArrayHasKey('4XX', $responses);
        $this->assertEquals('Successful', $responses['2XX']['description']);
        $this->assertEquals('Client error', $responses['4XX']['description']);
    }
}
