<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\Test\ServiceTestCase;
use WebFiori\Tests\Http\TestServices\AnnotatedMethodService;
use WebFiori\Tests\Http\TestServices\AllMethodsService;
use WebFiori\Tests\Http\TestServices\StringAuthDenialService;
use WebFiori\Tests\Http\TestServices\ParameterSetService;

/**
 * Tests for ServiceTestCase and TestResponse.
 * 
 * Demonstrates the new testing API using real services.
 */
class ServiceTestCaseTest extends ServiceTestCase {

    public function testGetRequest() {
        $this->get(new AnnotatedMethodService(), ['param1' => 'hello'])
            ->assertOk()
            ->assertJson();
    }

    public function testPostRequest() {
        $this->post(new AllMethodsService(), ['name' => 'John'])
            ->assertOk()
            ->assertJson();
    }

    public function testUnauthorizedResponse() {
        $this->get(new StringAuthDenialService())
            ->assertUnauthorized()
            ->assertError()
            ->assertJsonEquals('message', 'You must be a premium member to access this resource.');
    }

    public function testMethodNotAllowed() {
        $this->delete(new AnnotatedMethodService())
            ->assertError();
    }

    public function testWithAuthentication() {
        $user = new TestUser(1, ['USER'], [], true);

        $this->get(new \WebFiori\Tests\Http\TestServices\MethodRequiresAuthService(), [], $user)
            ->assertOk()
            ->assertJsonEquals('secret', 'method-level-protected');
    }

    public function testParameterSetService() {
        $this->get(new ParameterSetService(), ['page' => '2', 'per_page' => '50'])
            ->assertOk()
            ->assertJsonEquals('page', 2)
            ->assertJsonEquals('per_page', 50);
    }

    public function testParameterSetDefaults() {
        $this->get(new ParameterSetService())
            ->assertOk()
            ->assertJsonEquals('page', 1)
            ->assertJsonEquals('per_page', 20);
    }

    public function testAssertJsonHas() {
        $this->get(new ParameterSetService())
            ->assertJsonHas('page')
            ->assertJsonHas('per_page');
    }

    public function testAssertBodyContains() {
        $this->get(new ParameterSetService())
            ->assertBodyContains('"page"');
    }

    public function testGetStatusCode() {
        $response = $this->get(new StringAuthDenialService());
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testGetJson() {
        $response = $this->get(new ParameterSetService());
        $json = $response->getJson();
        $this->assertIsArray($json);
        $this->assertEquals(1, $json['page']);
    }

    public function testGetBody() {
        $response = $this->get(new ParameterSetService());
        $this->assertNotEmpty($response->getBody());
        $this->assertJson($response->getBody());
    }
}
