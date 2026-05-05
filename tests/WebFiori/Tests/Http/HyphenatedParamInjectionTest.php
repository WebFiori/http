<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\HyphenatedParamService;

/**
 * Tests that method parameter injection works correctly when request parameter
 * names are hyphenated and PHP variable names are arbitrary.
 *
 * Regression test for: https://github.com/WebFiori/http/issues/106
 */
class HyphenatedParamInjectionTest extends APITestCase {

    /**
     * Test that hyphenated params are injected positionally into method parameters.
     */
    public function testGetWithHyphenatedParams() {
        $manager = new WebServicesManager();
        $manager->addService(new HyphenatedParamService());

        $output = $this->getRequest($manager, 'hyphenated-param-service', [
            'app-id' => 42,
            'user-name' => 'Ibrahim',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals(42, $response['app-id']);
        $this->assertEquals('Ibrahim', $response['user-name']);
    }

    /**
     * Test that hyphenated params work with optional parameters omitted.
     */
    public function testGetWithOptionalParamOmitted() {
        $manager = new WebServicesManager();
        $manager->addService(new HyphenatedParamService());

        $output = $this->getRequest($manager, 'hyphenated-param-service', [
            'app-id' => 7,
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals(7, $response['app-id']);
        $this->assertNull($response['user-name']);
    }

    /**
     * Test POST with multiple hyphenated params injected positionally.
     */
    public function testPostWithMultipleHyphenatedParams() {
        $manager = new WebServicesManager();
        $manager->addService(new HyphenatedParamService());

        $output = $this->postRequest($manager, 'hyphenated-param-service', [
            'first-name' => 'John',
            'last-name' => 'Doe',
            'email-address' => 'john@example.com',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('John', $response['first-name']);
        $this->assertEquals('Doe', $response['last-name']);
        $this->assertEquals('john@example.com', $response['email-address']);
    }

    /**
     * Test that missing required hyphenated param is reported as error.
     */
    public function testMissingRequiredHyphenatedParam() {
        $manager = new WebServicesManager();
        $manager->addService(new HyphenatedParamService());

        $output = $this->getRequest($manager, 'hyphenated-param-service', []);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }
}
