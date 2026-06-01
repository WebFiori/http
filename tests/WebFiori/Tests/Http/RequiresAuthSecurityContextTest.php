<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\SecurityContext;
use WebFiori\Tests\Http\TestUser;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\ClassRequiresAuthService;
use WebFiori\Tests\Http\TestServices\MethodRequiresAuthService;

/**
 * Tests for #[RequiresAuth] checking SecurityContext::isAuthenticated() directly.
 * 
 * @see https://github.com/WebFiori/http/issues/117
 */
class RequiresAuthSecurityContextTest extends APITestCase {

    // =========================================================================
    // Method-level #[RequiresAuth]
    // =========================================================================

    public function testMethodRequiresAuthWithUser() {
        $user = new TestUser(1, ['USER'], [], true);

        $manager = new WebServicesManager();
        $manager->addService(new MethodRequiresAuthService());

        $output = $this->getRequest($manager, 'method-requires-auth', [], [], $user);
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals('method-level-protected', $response['secret']);
    }

    public function testMethodRequiresAuthWithoutUser() {
        SecurityContext::setCurrentUser(null);

        $manager = new WebServicesManager();
        $manager->addService(new MethodRequiresAuthService());

        $output = $this->getRequest($manager, 'method-requires-auth');
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        $this->assertEquals(401, $response['http-code']);
    }

    public function testMethodRequiresAuthIgnoresIsAuthorized() {
        $user = new TestUser(2, ['ADMIN'], [], true);

        $manager = new WebServicesManager();
        $manager->addService(new MethodRequiresAuthService());

        $output = $this->getRequest($manager, 'method-requires-auth', [], [], $user);
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals('method-level-protected', $response['secret']);
    }

    // =========================================================================
    // Class-level #[RequiresAuth]
    // =========================================================================

    public function testClassRequiresAuthWithUser() {
        $user = new TestUser(3, ['USER'], [], true);

        $manager = new WebServicesManager();
        $manager->addService(new ClassRequiresAuthService());

        $output = $this->getRequest($manager, 'class-requires-auth', [], [], $user);
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals('class-level-protected', $response['secret']);
    }

    public function testClassRequiresAuthWithoutUser() {
        SecurityContext::setCurrentUser(null);

        $manager = new WebServicesManager();
        $manager->addService(new ClassRequiresAuthService());

        $output = $this->getRequest($manager, 'class-requires-auth');
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        $this->assertEquals(401, $response['http-code']);
    }

    public function testClassRequiresAuthMethodAllowAnonymous() {
        // Class has #[RequiresAuth] but method has #[AllowAnonymous]
        SecurityContext::setCurrentUser(null);

        $manager = new WebServicesManager();
        $manager->addService(new ClassRequiresAuthService());

        $output = $this->postRequest($manager, 'class-requires-auth');
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals(true, $response['public']);
    }

    // =========================================================================
    // No attributes — traditional fallback
    // =========================================================================

    public function testNoAttributesFallsBackToIsAuthorized() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('no-attr-deny');
                $this->addRequestMethod('GET');
                $this->setIsAuthRequired(true);
            }
            public function isAuthorized(): bool {
                return false;
            }
            public function processRequest() {
                $this->sendResponse('should not reach', 200, 'success');
            }
        };

        $manager = new WebServicesManager();
        $manager->addService($service);

        $output = $this->getRequest($manager, 'no-attr-deny');
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        $this->assertEquals(401, $response['http-code']);
    }

    public function testNoAttributesIsAuthorizedTrue() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('no-attr-allow');
                $this->addRequestMethod('GET');
                $this->setIsAuthRequired(true);
            }
            public function isAuthorized(): bool {
                return true;
            }
            public function processRequest() {
                $this->sendResponse('allowed', 200, 'success');
            }
        };

        $manager = new WebServicesManager();
        $manager->addService($service);

        $output = $this->getRequest($manager, 'no-attr-allow');
        $response = json_decode($output, true);

        $this->assertIsArray($response);
        $this->assertEquals('success', $response['type']);
    }
}
