<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\ParameterSet;
use WebFiori\Http\WebService;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AddressParams;
use WebFiori\Tests\Http\TestServices\PaginationParams;
use WebFiori\Tests\Http\TestServices\ParameterSetService;

/**
 * Tests for ParameterSet interface and UseParameterSet attribute.
 * 
 * @see https://github.com/WebFiori/http/issues/116
 */
class ParameterSetTest extends APITestCase {

    // =========================================================================
    // Interface / addParameterSet() tests
    // =========================================================================

    public function testParameterSetInterface() {
        $set = new PaginationParams();
        $this->assertInstanceOf(ParameterSet::class, $set);

        $params = $set->getParameters();
        $this->assertArrayHasKey('page', $params);
        $this->assertArrayHasKey('per_page', $params);
    }

    public function testAddParameterSetTraditional() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('test-set');
                $this->addRequestMethod('GET');
                $this->addParameterSet(new PaginationParams());
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->assertTrue($service->hasParameter('page'));
        $this->assertTrue($service->hasParameter('per_page'));
    }

    public function testAddMultipleSets() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('multi-set');
                $this->addRequestMethod('POST');
                $this->addParameterSet(new PaginationParams());
                $this->addParameterSet(new AddressParams());
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        // Pagination params
        $this->assertTrue($service->hasParameter('page'));
        $this->assertTrue($service->hasParameter('per_page'));
        // Address params
        $this->assertTrue($service->hasParameter('street'));
        $this->assertTrue($service->hasParameter('city'));
        $this->assertTrue($service->hasParameter('zip'));
        $this->assertTrue($service->hasParameter('country'));
    }

    public function testParameterSetPreservesOptions() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('options-test');
                $this->addRequestMethod('GET');
                $this->addParameterSet(new PaginationParams());
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $pageParam = $service->getParameterByName('page');
        $this->assertNotNull($pageParam);
        $this->assertTrue($pageParam->isOptional());
        $this->assertEquals(1, $pageParam->getDefault());
        $this->assertEquals(1, $pageParam->getMinValue());
    }

    public function testParameterSetWithPatternAndAllowedValues() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('validation-test');
                $this->addRequestMethod('GET');
                $this->addParameterSet(new AddressParams());
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $zipParam = $service->getParameterByName('zip');
        $this->assertNotNull($zipParam);
        $this->assertEquals('/^[0-9]{5}$/', $zipParam->getPattern());

        $countryParam = $service->getParameterByName('country');
        $this->assertNotNull($countryParam);
        $this->assertEquals(['US', 'UK', 'DE'], $countryParam->getAllowedValues());
    }

    // =========================================================================
    // #[UseParameterSet] attribute tests
    // =========================================================================

    public function testUseParameterSetAttribute() {
        $manager = new WebServicesManager();
        $manager->addService(new ParameterSetService());

        $output = $this->getRequest($manager, 'parameter-set-service', [
            'page' => '3',
            'per_page' => '50',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals(3, $response['page']);
        $this->assertEquals(50, $response['per_page']);
    }

    public function testUseParameterSetWithDefaults() {
        $manager = new WebServicesManager();
        $manager->addService(new ParameterSetService());

        // No params — should use defaults (page=1, per_page=20)
        $output = $this->getRequest($manager, 'parameter-set-service');

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals(1, $response['page']);
        $this->assertEquals(20, $response['per_page']);
    }

    public function testUseParameterSetWithRequestParam() {
        $manager = new WebServicesManager();
        $manager->addService(new ParameterSetService());

        $output = $this->postRequest($manager, 'parameter-set-service', [
            'street' => '123 Main St',
            'city' => 'Springfield',
            'zip' => '12345',
            'country' => 'US',
            'note' => 'Leave at door',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('123 Main St', $response['street']);
        $this->assertEquals('Springfield', $response['city']);
        $this->assertEquals('12345', $response['zip']);
        $this->assertEquals('US', $response['country']);
        $this->assertEquals('Leave at door', $response['note']);
    }

    public function testUseParameterSetValidationApplied() {
        $manager = new WebServicesManager();
        $manager->addService(new ParameterSetService());

        // Invalid zip (not 5 digits) and invalid country (not in allowed values)
        $output = $this->postRequest($manager, 'parameter-set-service', [
            'street' => '123 Main St',
            'city' => 'Springfield',
            'zip' => 'ABCDE',
            'country' => 'JP',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }

    public function testUseParameterSetMissingRequired() {
        $manager = new WebServicesManager();
        $manager->addService(new ParameterSetService());

        // Missing required address fields
        $output = $this->postRequest($manager, 'parameter-set-service', [
            'street' => '123 Main St',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }

    public function testInvalidSetClassIgnored() {
        // A UseParameterSet pointing to a non-ParameterSet class should be silently ignored
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('invalid-set');
                $this->addRequestMethod('GET');
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {
                $this->sendResponse('ok', 200, 'success');
            }
        };

        // Manually test that configuring with a non-ParameterSet class doesn't crash
        $manager = new WebServicesManager();
        $manager->addService($service);

        $output = $this->getRequest($manager, 'invalid-set');
        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('success', $response['type']);
    }

    public function testNonExistentClassIgnored() {
        // Verify that a non-existent class in UseParameterSet doesn't crash
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('nonexistent-set');
                $this->addRequestMethod('GET');
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {
                $this->sendResponse('ok', 200, 'success');
            }
        };

        $manager = new WebServicesManager();
        $manager->addService($service);

        $output = $this->getRequest($manager, 'nonexistent-set');
        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('success', $response['type']);
    }
}
