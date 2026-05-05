<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\ResponseEntity;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\ResponseEntityLoginService;
use WebFiori\Tests\Http\TestServices\ResponseEntityItemsService;
use WebFiori\Tests\Http\TestServices\ResponseEntityMiscService;
use WebFiori\Json\Json;

/**
 * Comprehensive tests for ResponseEntity class and its integration with
 * #[ResponseBody] method handling.
 *
 * Feature request: https://github.com/WebFiori/http/issues/107
 */
class ResponseEntityTest extends APITestCase {

    // =========================================================================
    // Unit tests for ResponseEntity class
    // =========================================================================

    public function testConstructorDefaults() {
        $entity = new ResponseEntity(['key' => 'value']);
        $this->assertEquals(200, $entity->getStatus());
        $this->assertEquals('application/json', $entity->getContentType());
        $this->assertEquals(['key' => 'value'], $entity->getBody());
    }

    public function testConstructorCustomValues() {
        $entity = new ResponseEntity('custom body', 418, 'text/plain');
        $this->assertEquals(418, $entity->getStatus());
        $this->assertEquals('text/plain', $entity->getContentType());
        $this->assertEquals('custom body', $entity->getBody());
    }

    public function testOkFactory() {
        $entity = ResponseEntity::ok('success');
        $this->assertEquals(200, $entity->getStatus());
        $this->assertEquals('success', $entity->getBody());
    }

    public function testCreatedFactory() {
        $entity = ResponseEntity::created(['id' => 1]);
        $this->assertEquals(201, $entity->getStatus());
        $this->assertEquals(['id' => 1], $entity->getBody());
    }

    public function testNoContentFactory() {
        $entity = ResponseEntity::noContent();
        $this->assertEquals(204, $entity->getStatus());
        $this->assertNull($entity->getBody());
    }

    public function testBadRequestFactory() {
        $entity = ResponseEntity::badRequest('bad');
        $this->assertEquals(400, $entity->getStatus());
        $this->assertEquals('bad', $entity->getBody());
    }

    public function testUnauthorizedFactory() {
        $entity = ResponseEntity::unauthorized('no auth');
        $this->assertEquals(401, $entity->getStatus());
        $this->assertEquals('no auth', $entity->getBody());
    }

    public function testForbiddenFactory() {
        $entity = ResponseEntity::forbidden('denied');
        $this->assertEquals(403, $entity->getStatus());
        $this->assertEquals('denied', $entity->getBody());
    }

    public function testNotFoundFactory() {
        $entity = ResponseEntity::notFound('missing');
        $this->assertEquals(404, $entity->getStatus());
        $this->assertEquals('missing', $entity->getBody());
    }

    public function testErrorFactory() {
        $entity = ResponseEntity::error('crash');
        $this->assertEquals(500, $entity->getStatus());
        $this->assertEquals('crash', $entity->getBody());
    }

    public function testWithJsonBody() {
        $json = new Json();
        $json->add('key', 'value');
        $entity = ResponseEntity::ok($json);
        $this->assertInstanceOf(Json::class, $entity->getBody());
    }

    public function testWithNullBody() {
        $entity = new ResponseEntity(null, 204);
        $this->assertNull($entity->getBody());
        $this->assertEquals(204, $entity->getStatus());
    }

    // =========================================================================
    // Integration tests: Login service (dynamic status codes)
    // =========================================================================

    public function testLoginSuccess() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityLoginService());

        $output = $this->postRequest($manager, 'response-entity-login', [
            'username' => 'admin',
            'password' => 'secret',
        ]);

        $response = json_decode($output, true);
        $this->assertEquals('abc123', $response['token']);
    }

    public function testLoginUnauthorized() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityLoginService());

        $output = $this->postRequest($manager, 'response-entity-login', [
            'username' => 'wrong',
            'password' => 'wrong',
        ]);

        $response = json_decode($output, true);
        $this->assertEquals('Invalid credentials', $response['message']);
    }

    public function testLoginForbidden() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityLoginService());

        $output = $this->postRequest($manager, 'response-entity-login', [
            'username' => 'banned',
            'password' => 'any',
        ]);

        $response = json_decode($output, true);
        $this->assertEquals('Account suspended', $response['message']);
    }

    // =========================================================================
    // Integration tests: Items service (CRUD with various status codes)
    // =========================================================================

    public function testGetItemSuccess() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityItemsService());

        $output = $this->getRequest($manager, 'response-entity-items', [
            'id' => 5,
        ]);

        $response = json_decode($output, true);
        $this->assertEquals(5, $response['id']);
        $this->assertEquals('Item 5', $response['name']);
    }

    public function testGetItemNotFound() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityItemsService());

        $output = $this->getRequest($manager, 'response-entity-items', [
            'id' => 999,
        ]);

        $response = json_decode($output, true);
        $this->assertEquals('Item not found', $response['message']);
    }

    public function testGetItemBadRequest() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityItemsService());

        $output = $this->getRequest($manager, 'response-entity-items', [
            'id' => 0,
        ]);

        $response = json_decode($output, true);
        $this->assertEquals('Invalid ID', $response['message']);
    }

    public function testDeleteItemNoContent() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityItemsService());

        $output = $this->deleteRequest($manager, 'response-entity-items', [
            'id' => 1,
        ]);

        $this->assertEmpty(trim($output));
    }

    public function testDeleteItemNotFound() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityItemsService());

        $output = $this->deleteRequest($manager, 'response-entity-items', [
            'id' => 999,
        ]);

        $response = json_decode($output, true);
        $this->assertEquals('Item not found', $response['message']);
    }

    public function testCreateItemWithArrayBody() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityItemsService());

        $output = $this->postRequest($manager, 'response-entity-items', [
            'name' => 'Widget',
        ]);

        $response = json_decode($output, true);
        $this->assertEquals(42, $response['data']['id']);
        $this->assertEquals('Widget', $response['data']['name']);
    }

    // =========================================================================
    // Integration tests: Misc (server error, custom content type)
    // =========================================================================

    public function testServerError() {
        $manager = new WebServicesManager();
        $manager->addService(new ResponseEntityMiscService());

        $output = $this->getRequest($manager, 'response-entity-misc');

        $response = json_decode($output, true);
        $this->assertEquals('Something went wrong', $response['message']);
    }
}
