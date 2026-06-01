<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AnnotatedParamsLegacyService;
use WebFiori\Tests\Http\TestServices\AnnotatedService;

/**
 * Tests that #[RequestParam] annotations are resolved before filtering
 * in WebServicesManager::process(), for services using the traditional
 * processRequest() pattern (without #[ResponseBody]).
 *
 * Regression test for: https://github.com/WebFiori/http/issues/102
 */
class AnnotatedParamsProcessRequestTest extends APITestCase {

    /**
     * Test that POST parameters are received via processRequest() when
     * using #[RequestParam] annotations without #[ResponseBody].
     */
    public function testPostParamsResolvedInProcessRequest() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedParamsLegacyService());

        $output = $this->postRequest($manager, 'annotated-params-legacy', [
            'username' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('success', $response['type']);
        $this->assertStringContainsString('admin@test.com', $response['message']);
    }

    /**
     * Test that missing required parameters are correctly reported
     * when using #[RequestParam] annotations without #[ResponseBody].
     */
    public function testMissingParamsReportedInProcessRequest() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedParamsLegacyService());

        $output = $this->postRequest($manager, 'annotated-params-legacy', []);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        // Framework should report missing required params
        $this->assertArrayHasKey('errors', $response['more-info']);
        $this->assertArrayHasKey('username', $response['more-info']['errors']);
        $this->assertArrayHasKey('password', $response['more-info']['errors']);
    }

    /**
     * Test that partially missing parameters are reported.
     */
    public function testPartialParamsMissing() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedParamsLegacyService());

        $output = $this->postRequest($manager, 'annotated-params-legacy', [
            'username' => 'admin@test.com',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        $this->assertArrayHasKey('password', $response['more-info']['errors']);
    }

    /**
     * Test that #[ResponseBody] services still work correctly after the fix.
     * This ensures the fix doesn't break the existing auto-processing path.
     */
    public function testResponseBodyServiceStillWorks() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedService());

        $output = $this->getRequest($manager, 'annotated-service', [
            'name' => 'Ibrahim',
        ]);

        $this->assertStringContainsString('Hi Ibrahim!', $output);
    }

    /**
     * Test that #[ResponseBody] service with no params still works.
     */
    public function testResponseBodyServiceNoParams() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedService());

        $output = $this->getRequest($manager, 'annotated-service');

        $this->assertStringContainsString('Hi user!', $output);
    }

    /**
     * Test wrong HTTP method is rejected for annotated legacy service.
     */
    public function testWrongMethodRejected() {
        $manager = new WebServicesManager();
        $manager->addService(new AnnotatedParamsLegacyService());

        // Service only accepts POST, sending GET should fail
        $output = $this->getRequest($manager, 'annotated-params-legacy', [
            'username' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }
}
