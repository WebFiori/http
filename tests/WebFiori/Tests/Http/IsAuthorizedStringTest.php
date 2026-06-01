<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\StringAuthDenialService;

/**
 * Tests for isAuthorized() returning string as denial reason.
 * 
 * @see https://github.com/WebFiori/http/issues/111
 */
class IsAuthorizedStringTest extends APITestCase {

    /**
     * Test that returning a string from isAuthorized() sends it as the 401 message.
     */
    public function testStringDenialReason() {
        $manager = new WebServicesManager();
        $manager->addService(new StringAuthDenialService());

        $output = $this->getRequest($manager, 'string-auth-denial');

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        $this->assertEquals(401, $response['http-code']);
        $this->assertEquals('You must be a premium member to access this resource.', $response['message']);
    }

    /**
     * Test that returning false still uses the default 401 message.
     */
    public function testBoolFalseDenialUsesDefault() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('bool-deny');
                $this->addRequestMethod('GET');
            }
            public function isAuthorized(): bool {
                return false;
            }
            public function processRequest() {
                $this->sendResponse('Should not reach here', 200, 'success');
            }
        };

        $manager = new WebServicesManager();
        $manager->addService($service);

        $output = $this->getRequest($manager, 'bool-deny');

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        $this->assertEquals(401, $response['http-code']);
        $this->assertNotEmpty($response['message']);
    }

    /**
     * Test that returning true allows the request through.
     */
    public function testTrueAllowsAccess() {
        // Use a service that returns true from isAuthorized
        $manager = new WebServicesManager();
        // NoAuthService returns false, but let's use an inline service
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('auth-pass-test');
                $this->addRequestMethod('GET');
            }
            public function isAuthorized(): string|bool {
                return true;
            }
            public function processRequest() {
                $this->sendResponse('Access granted', 200, 'success');
            }
        };

        $manager->addService($service);
        $output = $this->getRequest($manager, 'auth-pass-test');

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('success', $response['type']);
        $this->assertEquals('Access granted', $response['message']);
    }

    /**
     * Test different denial reasons for different conditions.
     */
    public function testMultipleDenialReasons() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('multi-reason');
                $this->addRequestMethod('GET');
            }
            public function isAuthorized(): string|bool {
                $token = $this->getAuthHeader();
                if ($token === null) {
                    return 'Authentication token is required.';
                }
                return 'Insufficient privileges.';
            }
            public function processRequest() {
                $this->sendResponse('OK', 200, 'success');
            }
        };

        $manager = new WebServicesManager();
        $manager->addService($service);

        // No auth header — should get "token required" message
        $output = $this->getRequest($manager, 'multi-reason');
        $response = json_decode($output, true);
        $this->assertEquals('Authentication token is required.', $response['message']);
    }
}
