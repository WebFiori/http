<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\Test\ServiceTestCase;
use WebFiori\Tests\Http\TestServices\ValidationHookService;
use WebFiori\Tests\Http\TestServices\ServiceWideValidationService;

/**
 * Tests for cross-field validation hook (#115).
 * 
 * Covers:
 * - Service-wide validate() method
 * - Method-specific #[Validate] attribute
 * - Both running together with merged errors
 * - Missing validator method throws exception
 * - Validation passes → method invoked
 */
class CrossFieldValidationTest extends ServiceTestCase {

    // =========================================================================
    // Happy path — validation passes
    // =========================================================================

    public function testValidationPassesAllowsExecution() {
        $this->post(new ValidationHookService(), [
            'email' => 'user@example.com',
            'password' => 'securepass123',
            'password_confirm' => 'securepass123',
        ])
            ->assertOk()
            ->assertJsonHas('data');
    }

    public function testServiceWideValidationPasses() {
        $this->post(new ServiceWideValidationService(), [
            'start' => '1',
            'end' => '10',
        ])
            ->assertOk()
            ->assertJsonHas('data');
    }

    // =========================================================================
    // Method-specific #[Validate] failures
    // =========================================================================

    public function testPasswordMismatchFails() {
        $this->post(new ValidationHookService(), [
            'email' => 'user@example.com',
            'password' => 'securepass123',
            'password_confirm' => 'different',
        ])
            ->assertStatus(422)
            ->assertError()
            ->assertJsonHas('more-info');
    }

    public function testPasswordTooShortFails() {
        $this->post(new ValidationHookService(), [
            'email' => 'user@example.com',
            'password' => 'short',
            'password_confirm' => 'short',
        ])
            ->assertStatus(422)
            ->assertError();
    }

    // =========================================================================
    // Service-wide validate() failures
    // =========================================================================

    public function testServiceWideValidationRejectsInvalidInput() {
        $this->post(new ServiceWideValidationService(), [
            'start' => '10',
            'end' => '5',
        ])
            ->assertStatus(422)
            ->assertError();
    }

    public function testServiceWidePlusAddressingRejected() {
        $this->post(new ValidationHookService(), [
            'email' => 'user%2Btag@example.com',
            'password' => 'securepass123',
            'password_confirm' => 'securepass123',
        ])
            ->assertStatus(422)
            ->assertError();
    }

    // =========================================================================
    // Both validators run — errors merged
    // =========================================================================

    public function testBothValidatorsRunErrorsMerged() {
        $this->post(new ValidationHookService(), [
            'email' => 'user%2Btag@example.com',
            'password' => 'short',
            'password_confirm' => 'different',
        ])
            ->assertStatus(422)
            ->assertError();
    }

    // =========================================================================
    // Missing validator method
    // =========================================================================

    public function testMissingValidatorMethodThrowsException() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('bad-validator');
                $this->addRequestMethod('POST');
            }
            #[\WebFiori\Http\Annotations\PostMapping]
            #[\WebFiori\Http\Annotations\ResponseBody]
            #[\WebFiori\Http\Annotations\AllowAnonymous]
            #[\WebFiori\Http\Annotations\Validate('nonExistentMethod')]
            #[\WebFiori\Http\Annotations\RequestParam('name', 'string')]
            public function doSomething(string $name): array {
                return ['name' => $name];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $response = $this->post($service, ['name' => 'test']);
        $response->assertBodyContains('nonExistentMethod')
            ->assertBodyContains('error');
    }

    // =========================================================================
    // No validation defined — works normally
    // =========================================================================

    public function testNoValidationDefinedWorksNormally() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('no-validation');
                $this->addRequestMethod('POST');
            }
            #[\WebFiori\Http\Annotations\PostMapping]
            #[\WebFiori\Http\Annotations\ResponseBody]
            #[\WebFiori\Http\Annotations\AllowAnonymous]
            #[\WebFiori\Http\Annotations\RequestParam('value', 'string')]
            public function store(string $value): array {
                return ['stored' => $value];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->post($service, ['value' => 'hello'])
            ->assertOk()
            ->assertJsonHas('data');
    }

    // =========================================================================
    // Validate returns empty array — passes
    // =========================================================================

    public function testValidateReturnsEmptyArrayPasses() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('empty-validate');
                $this->addRequestMethod('POST');
            }
            public function validate(array $inputs): array {
                return []; // Always passes
            }
            #[\WebFiori\Http\Annotations\PostMapping]
            #[\WebFiori\Http\Annotations\ResponseBody]
            #[\WebFiori\Http\Annotations\AllowAnonymous]
            #[\WebFiori\Http\Annotations\RequestParam('x', 'string')]
            public function process(string $x): array {
                return ['x' => $x];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $this->post($service, ['x' => 'test'])
            ->assertOk()
            ->assertJsonHas('data');
    }
}
