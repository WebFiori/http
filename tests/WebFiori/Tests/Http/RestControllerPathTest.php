<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Http\WebServicesManager;

/**
 * Tests for the path property on #[RestController].
 *
 * @see https://github.com/WebFiori/http/issues/141
 */
class RestControllerPathTest extends TestCase {

    /**
     * Service with path set should use path for OpenAPI spec.
     */
    public function testPathUsedInOpenAPISpec() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('login');
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        $service->setPath('auth/login');

        $manager = new WebServicesManager();
        $manager->addService($service);
        $json = json_decode($manager->toOpenAPI()->toJSON() . '', true);

        $this->assertArrayHasKey('/auth/login', $json['paths']);
        $this->assertArrayNotHasKey('/login', $json['paths']);
    }

    /**
     * Service without path should fall back to name for OpenAPI spec.
     */
    public function testNoPathFallsBackToName() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('users');
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $manager = new WebServicesManager();
        $manager->addService($service);
        $json = json_decode($manager->toOpenAPI()->toJSON() . '', true);

        $this->assertArrayHasKey('/users', $json['paths']);
    }

    /**
     * #[RestController] with path property should configure the service path.
     */
    public function testAnnotationPathProperty() {
        $service = new PathAnnotatedService();

        $this->assertEquals('login', $service->getName());
        $this->assertEquals('auth/login', $service->getPath());
    }

    /**
     * #[RestController] with path should generate correct OpenAPI path.
     */
    public function testAnnotationPathInOpenAPI() {
        $manager = new WebServicesManager();
        $manager->addService(new PathAnnotatedService());
        $json = json_decode($manager->toOpenAPI()->toJSON() . '', true);

        $this->assertArrayHasKey('/auth/login', $json['paths']);
    }

    /**
     * #[RestController] without path should use name as path.
     */
    public function testAnnotationWithoutPath() {
        $service = new NoPathAnnotatedService();

        $this->assertEquals('users', $service->getName());
        $this->assertEquals('users', $service->getPath());
    }

    /**
     * #[RestController] with only path (no name) derives name from class.
     */
    public function testAnnotationPathOnlyNoName() {
        $service = new PathOnlyService();

        $this->assertEquals('v2/items', $service->getPath());
    }

    /**
     * setPath trims leading/trailing slashes.
     */
    public function testSetPathTrimsSlashes() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('test');
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $service->setPath('/api/v2/users/');
        $this->assertEquals('api/v2/users', $service->getPath());
    }

    /**
     * Base path in manager combined with service path.
     */
    public function testBasePathWithServicePath() {
        $service = new PathAnnotatedService();

        $manager = new WebServicesManager();
        $manager->setBasePath('/api/v1');
        $manager->addService($service);
        $json = json_decode($manager->toOpenAPI()->toJSON() . '', true);

        $this->assertArrayHasKey('/api/v1/auth/login', $json['paths']);
    }
}

#[RestController(name: 'login', path: 'auth/login')]
class PathAnnotatedService extends WebService {
    public function isAuthorized(): bool { return true; }

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function handle(): array {
        return ['token' => 'abc'];
    }

    public function processRequest() {}
}

#[RestController(name: 'users')]
class NoPathAnnotatedService extends WebService {
    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}

#[RestController(path: 'v2/items')]
class PathOnlyService extends WebService {
    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}
