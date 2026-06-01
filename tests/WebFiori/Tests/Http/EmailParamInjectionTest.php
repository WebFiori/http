<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\Request;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\EmailParamInjectionService;

/**
 * Regression test for: https://github.com/WebFiori/http/issues/112
 *
 * Method parameter injection passes validation result ("1") instead of
 * the actual email value for ParamType::EMAIL.
 */
class EmailParamInjectionTest extends APITestCase {

    /**
     * Test that EMAIL param injection passes the actual email string,
     * not the validation result.
     */
    public function testEmailValueInjected() {
        $manager = new WebServicesManager();
        $manager->addService(new EmailParamInjectionService());

        $output = $this->postRequest($manager, 'email-param-injection', [
            'email' => 'user@example.com',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('user@example.com', $response['email']);
    }

    /**
     * Test with a different valid email to confirm it's not hardcoded.
     */
    public function testAnotherEmailValueInjected() {
        $manager = new WebServicesManager();
        $manager->addService(new EmailParamInjectionService());

        $output = $this->postRequest($manager, 'email-param-injection', [
            'email' => 'admin@webfiori.com',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('admin@webfiori.com', $response['email']);
    }

    /**
     * Test that EMAIL param injection works correctly with JSON content type.
     * This is the scenario described in issue #112.
     */
    public function testEmailValueInjectedWithJsonBody() {
        $manager = new WebServicesManager();
        $manager->addService(new EmailParamInjectionService());

        // Write JSON body to a temp file to simulate php://input
        $jsonFile = sys_get_temp_dir() . '/email_test_input.json';
        file_put_contents($jsonFile, json_encode([
            'service' => 'email-param-injection',
            'email' => 'user@example.com',
        ]));

        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_POST = [];
        $_POST['service'] = 'email-param-injection';

        $manager->setInputStream($jsonFile);
        $manager->setOutputStream(fopen($this->getOutputFile(), 'w'));
        $manager->setRequest(\WebFiori\Http\Request::createFromGlobals());
        $manager->process();

        $output = $manager->readOutputStream();
        @unlink($jsonFile);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('user@example.com', $response['email']);
    }

    /**
     * Test that invalid email is rejected.
     */
    public function testInvalidEmailRejected() {
        $manager = new WebServicesManager();
        $manager->addService(new EmailParamInjectionService());

        $output = $this->postRequest($manager, 'email-param-injection', [
            'email' => 'not-an-email',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }
}
