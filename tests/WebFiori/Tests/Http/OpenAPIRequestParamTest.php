<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AnnotatedService;
use WebFiori\Tests\Http\TestServices\AnnotatedParamsLegacyService;
use WebFiori\Tests\Http\TestServices\OpenAPIGetPostService;

/**
 * Tests that #[RequestParam] annotations are included in OpenAPI spec generation.
 *
 * @see https://github.com/WebFiori/http/issues/100
 */
class OpenAPIRequestParamTest extends TestCase {

    /**
     * GET params should appear as query parameters in the spec.
     */
    public function testGetParamsAsQueryParameters() {
        $service = new AnnotatedService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        // GET operation should have parameters
        $this->assertArrayHasKey('parameters', $json['get']);
        $params = $json['get']['parameters'];

        // 'name' param: optional string
        $this->assertEquals('name', $params[0]['name']);
        $this->assertEquals('query', $params[0]['in']);
        $this->assertArrayNotHasKey('required', $params[0]); // optional
        $this->assertEquals('string', $params[0]['schema']['type']);
    }

    /**
     * DELETE params should appear as query parameters with required flag.
     */
    public function testDeleteParamsAsQueryParameters() {
        $service = new AnnotatedService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $params = $json['delete']['parameters'];

        // 'id' param: required integer
        $this->assertEquals('id', $params[0]['name']);
        $this->assertEquals('query', $params[0]['in']);
        $this->assertTrue($params[0]['required']);
        $this->assertEquals('integer', $params[0]['schema']['type']);
    }

    /**
     * POST params should appear as requestBody with schema properties.
     */
    public function testPostParamsAsRequestBody() {
        $service = new AnnotatedParamsLegacyService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $this->assertArrayHasKey('requestBody', $json['post']);
        $schema = $json['post']['requestBody']['content']['application/x-www-form-urlencoded']['schema'];

        $this->assertEquals('object', $schema['type']);
        $this->assertArrayHasKey('username', $schema['properties']);
        $this->assertArrayHasKey('password', $schema['properties']);
        $this->assertEquals('string', $schema['properties']['username']['type']);
        $this->assertEquals('string', $schema['properties']['password']['type']);
    }

    /**
     * Required POST params should be listed in the required array.
     */
    public function testPostRequiredParams() {
        $service = new AnnotatedParamsLegacyService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $schema = $json['post']['requestBody']['content']['application/x-www-form-urlencoded']['schema'];

        $this->assertContains('username', $schema['required']);
        $this->assertContains('password', $schema['required']);
    }

    /**
     * Service with mixed GET and POST methods should generate both
     * query parameters and requestBody.
     */
    public function testMixedGetAndPostService() {
        $service = new OpenAPIGetPostService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        // GET should have query parameters
        $this->assertArrayHasKey('parameters', $json['get']);
        $this->assertEquals('id', $json['get']['parameters'][0]['name']);
        $this->assertTrue($json['get']['parameters'][0]['required']);
        $this->assertEquals('integer', $json['get']['parameters'][0]['schema']['type']);

        // POST should have requestBody
        $this->assertArrayHasKey('requestBody', $json['post']);
        $props = $json['post']['requestBody']['content']['application/x-www-form-urlencoded']['schema']['properties'];
        $this->assertArrayHasKey('name', $props);
        $this->assertArrayHasKey('email', $props);
    }

    /**
     * Email type should produce string type with email format.
     */
    public function testEmailTypeFormat() {
        $service = new OpenAPIGetPostService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $props = $json['post']['requestBody']['content']['application/x-www-form-urlencoded']['schema']['properties'];
        $this->assertEquals('string', $props['email']['type']);
        $this->assertEquals('email', $props['email']['format']);
    }

    /**
     * Optional POST params should NOT appear in the required array.
     */
    public function testOptionalPostParamNotInRequired() {
        $service = new OpenAPIGetPostService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $schema = $json['post']['requestBody']['content']['application/x-www-form-urlencoded']['schema'];
        $this->assertContains('name', $schema['required']);
        $this->assertContains('email', $schema['required']);
        $this->assertNotContains('nickname', $schema['required'] ?? []);
    }

    /**
     * toOpenAPI on the manager should include parameters in paths.
     */
    public function testManagerToOpenAPIIncludesParams() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedService());
        $openapi = $manager->toOpenAPI();
        $json = json_decode($openapi->toJSON() . '', true);

        $path = $json['paths']['/annotated-service'];
        $this->assertArrayHasKey('parameters', $path['get']);
        $this->assertEquals('name', $path['get']['parameters'][0]['name']);
    }

    /**
     * Service with no #[RequestParam] should produce no parameters or requestBody.
     */
    public function testServiceWithNoParamsHasNoParametersInSpec() {
        $service = new \WebFiori\Tests\Http\TestServices\LegacyService();
        $pathItem = $service->toPathItemObj();
        $json = json_decode($pathItem->toJSON() . '', true);

        $this->assertArrayNotHasKey('parameters', $json['get']);
        $this->assertArrayNotHasKey('requestBody', $json['get']);
    }
}
