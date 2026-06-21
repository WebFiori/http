<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\OpenAPI\OpenAPIGenerator;
use WebFiori\Http\OpenAPI\OpenAPISpecService;
use WebFiori\Http\Test\ServiceTestCase;
use WebFiori\Tests\Http\TestServices\ApiResponseAnnotatedService;
use WebFiori\Tests\Http\TestServices\OpenAPIGetPostService;

/**
 * Tests for OpenAPI namespace scanning and OpenAPISpecService.
 *
 * @see https://github.com/WebFiori/http/issues/144
 */
class OpenAPINamespaceScanTest extends ServiceTestCase {

    /**
     * discoverServices finds classes with #[RestController] in a namespace.
     */
    public function testDiscoverServicesFindsAnnotatedClasses() {
        // Ensure classes are loaded
        class_exists(ApiResponseAnnotatedService::class);
        class_exists(OpenAPIGetPostService::class);

        $services = OpenAPIGenerator::discoverServices('WebFiori\\Tests\\Http\\TestServices');

        $names = array_map(fn($s) => $s->getName(), $services);
        $this->assertContains('api-response-service', $names);
        $this->assertContains('get-post-service', $names);
    }

    /**
     * discoverServices excludes classes without #[RestController].
     */
    public function testDiscoverServicesExcludesNonAnnotated() {
        $services = OpenAPIGenerator::discoverServices('WebFiori\\Tests\\Http\\TestServices');

        $names = array_map(fn($s) => $s->getName(), $services);
        // NonAnnotatedService exists but has no #[RestController]
        $this->assertNotContains('non-annotated', $names);
    }

    /**
     * discoverServices excludes abstract classes.
     */
    public function testDiscoverServicesExcludesAbstract() {
        $services = OpenAPIGenerator::discoverServices('WebFiori\\Tests\\Http\\TestServices');

        $classes = array_map(fn($s) => get_class($s), $services);
        foreach ($classes as $class) {
            $this->assertFalse((new \ReflectionClass($class))->isAbstract());
        }
    }

    /**
     * discoverServices returns empty for non-existent namespace.
     */
    public function testDiscoverServicesEmptyForUnknownNamespace() {
        $services = OpenAPIGenerator::discoverServices('NonExistent\\Namespace');
        $this->assertEmpty($services);
    }

    /**
     * generateFromNamespace produces a valid OpenAPI spec.
     */
    public function testGenerateFromNamespace() {
        // Ensure classes are loaded
        class_exists(OpenAPIGetPostService::class);

        $generator = new OpenAPIGenerator();
        $spec = $generator->generateFromNamespace(
            'WebFiori\\Tests\\Http\\TestServices',
            'Test API',
            '2.0.0',
            '/api'
        );

        $json = json_decode($spec->toJSON() . '', true);
        $this->assertEquals('3.1.0', $json['openapi']);
        $this->assertEquals('Test API', $json['info']['title']);
        $this->assertEquals('2.0.0', $json['info']['version']);
        $this->assertArrayHasKey('/api/get-post-service', $json['paths']);
    }

    /**
     * OpenAPISpecService returns spec via GET.
     */
    public function testOpenAPISpecServiceReturnsSpec() {
        // Ensure classes are loaded
        class_exists(OpenAPIGetPostService::class);

        $service = new OpenAPISpecService(
            'WebFiori\\Tests\\Http\\TestServices',
            '/api',
            'My API',
            '3.0.0'
        );

        $response = $this->get($service);
        $body = $response->getBody();
        $decoded = json_decode($body, true);

        $this->assertNotNull($decoded, 'Response should be valid JSON');
        $this->assertEquals('3.1.0', $decoded['openapi']);
        $this->assertEquals('My API', $decoded['info']['title']);
        $this->assertEquals('3.0.0', $decoded['info']['version']);
        $this->assertArrayHasKey('paths', $decoded);
    }

    /**
     * OpenAPISpecService response contains no metadata.
     */
    public function testOpenAPISpecServiceCleanOutput() {
        class_exists(OpenAPIGetPostService::class);

        $service = new OpenAPISpecService(
            'WebFiori\\Tests\\Http\\TestServices',
            '/api',
            'Test',
            '1.0.0'
        );

        $response = $this->get($service);
        $body = $response->getBody();

        $this->assertStringNotContainsString('Value', $body);
        $this->assertStringNotContainsString('JsonXTagName', $body);
        $this->assertStringNotContainsString('"data"', $body);
    }
}
