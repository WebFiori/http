<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\OpenAPI\OpenAPIGenerator;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AnnotatedMethodService;
use WebFiori\Tests\Http\TestServices\AllMethodsService;

/**
 * Tests for OpenAPIGenerator.
 * 
 * @see https://github.com/WebFiori/http/issues/120
 */
class OpenAPIGeneratorTest extends TestCase {

    public function testGenerateWithSingleService() {
        $generator = new OpenAPIGenerator();
        $service = new AnnotatedMethodService();

        $spec = $generator->generate([$service], 'Test API', '1.0.0');
        $json = $spec->toJSON();

        $this->assertEquals('3.1.0', $json->get('openapi'));
        $this->assertEquals('Test API', $json->get('info')->get('title'));
        $this->assertEquals('1.0.0', $json->get('info')->get('version'));
        $this->assertTrue($json->get('paths')->hasKey('/' . $service->getName()));
    }

    public function testGenerateWithMultipleServices() {
        $generator = new OpenAPIGenerator();
        $service1 = new AnnotatedMethodService();
        $service2 = new AllMethodsService();

        $spec = $generator->generate([$service1, $service2], 'Multi API', '2.0.0');
        $json = $spec->toJSON();

        $this->assertTrue($json->get('paths')->hasKey('/' . $service1->getName()));
        $this->assertTrue($json->get('paths')->hasKey('/' . $service2->getName()));
    }

    public function testGenerateWithBasePath() {
        $generator = new OpenAPIGenerator();
        $service = new AnnotatedMethodService();

        $spec = $generator->generate([$service], 'API', '1.0.0', '/api/v2');
        $json = $spec->toJSON();

        $this->assertTrue($json->get('paths')->hasKey('/api/v2/' . $service->getName()));
    }

    public function testGenerateWithDescriptionAndVersion() {
        $generator = new OpenAPIGenerator();

        $spec = $generator->generate([], 'My Great API', '3.5.1');
        $json = $spec->toJSON();

        $this->assertEquals('My Great API', $json->get('info')->get('title'));
        $this->assertEquals('3.5.1', $json->get('info')->get('version'));
    }

    public function testGenerateEmptyServices() {
        $generator = new OpenAPIGenerator();

        $spec = $generator->generate([]);
        $json = $spec->toJSON();

        $this->assertEquals('3.1.0', $json->get('openapi'));
        // Paths object exists but has no paths
        $this->assertNotNull($json->get('paths'));
    }

    public function testDeprecatedManagerMethodStillWorks() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedMethodService());
        $manager->setDescription('Legacy API');
        $manager->setVersion('1.2.3');
        $manager->setBasePath('/legacy');

        $spec = $manager->toOpenAPI();
        $json = $spec->toJSON();

        $this->assertEquals('Legacy API', $json->get('info')->get('title'));
        $this->assertEquals('1.2.3', $json->get('info')->get('version'));
        $service = new AnnotatedMethodService();
        $this->assertTrue($json->get('paths')->hasKey('/legacy/' . $service->getName()));
    }
}
