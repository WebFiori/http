<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AllowEmptyParamService;

class AllowEmptyParamTest extends APITestCase {

    /**
     * Test that an empty string is accepted when allowEmpty is true.
     */
    public function testEmptyStringAccepted() {
        $manager = new WebServicesManager();
        $manager->addService(new AllowEmptyParamService());

        $output = $this->postRequest($manager, 'allow-empty-param', [
            'title' => 'My Title',
            'notes' => '',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('My Title', $response['data']['title']);
        $this->assertEquals('', $response['data']['notes']);
    }

    /**
     * Test that omitting the optional parameter still works.
     */
    public function testOmittedOptionalParam() {
        $manager = new WebServicesManager();
        $manager->addService(new AllowEmptyParamService());

        $output = $this->postRequest($manager, 'allow-empty-param', [
            'title' => 'My Title',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('My Title', $response['data']['title']);
        $this->assertEquals('', $response['data']['notes']);
    }

    /**
     * Test that a non-empty value is accepted normally.
     */
    public function testNonEmptyValue() {
        $manager = new WebServicesManager();
        $manager->addService(new AllowEmptyParamService());

        $output = $this->postRequest($manager, 'allow-empty-param', [
            'title' => 'My Title',
            'notes' => 'Some notes here',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('My Title', $response['data']['title']);
        $this->assertEquals('Some notes here', $response['data']['notes']);
    }
}
