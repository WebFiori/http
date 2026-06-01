<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\Test\ServiceTestCase;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\ParamOption;
use WebFiori\Http\WebService;
use WebFiori\Http\ErrorResponse;
use WebFiori\Http\RequestParameter;

/**
 * Tests for #113: validation error status code (422) and custom messages.
 */
class ValidationErrorMessagesTest extends ServiceTestCase {

    public function testInvalidParamsReturns422() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('test-422');
                $this->addRequestMethod('POST');
                $this->addParameters([
                    'email' => [ParamOption::TYPE => ParamType::EMAIL],
                ]);
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() { $this->sendResponse('ok'); }
        };

        $this->post($service, ['email' => 'not-an-email'])
            ->assertStatus(422)
            ->assertError();
    }

    public function testMissingParamsReturns422() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('test-missing-422');
                $this->addRequestMethod('POST');
                $this->addParameters([
                    'name' => [ParamOption::TYPE => ParamType::STRING],
                ]);
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() { $this->sendResponse('ok'); }
        };

        $this->post($service, [])
            ->assertStatus(422)
            ->assertError();
    }

    public function testCustomMessageInResponse() {
        $param = new RequestParameter('age', ParamType::INT);
        $param->setMessage('You must be at least 18 years old.');

        $result = ErrorResponse::invalidParams([$param]);
        $json = $result['json'];

        $this->assertEquals(422, $result['code']);
        $this->assertEquals('You must be at least 18 years old.', $json->get('more-info')->get('errors')->get('age'));
    }

    public function testDefaultMessageWhenNoCustom() {
        $param = new RequestParameter('email', ParamType::EMAIL);

        $result = ErrorResponse::invalidParams([$param]);
        $json = $result['json'];

        $this->assertEquals("Invalid value for parameter 'email'.", $json->get('more-info')->get('errors')->get('email'));
    }

    public function testMixedMessagesInResponse() {
        $paramWithMsg = new RequestParameter('age', ParamType::INT);
        $paramWithMsg->setMessage('Must be 18+.');

        $paramWithout = new RequestParameter('name', ParamType::STRING);

        $result = ErrorResponse::invalidParams([$paramWithMsg, $paramWithout]);
        $json = $result['json'];
        $errors = $json->get('more-info')->get('errors');

        $this->assertEquals('Must be 18+.', $errors->get('age'));
        $this->assertEquals("Invalid value for parameter 'name'.", $errors->get('name'));
    }

    public function testCustomMessageViaAttribute() {
        $service = new class extends WebService {
            public function __construct() {
                parent::__construct('attr-msg');
                $this->addRequestMethod('POST');
            }
            #[PostMapping]
            #[ResponseBody]
            #[AllowAnonymous]
            #[RequestParam('email', ParamType::EMAIL, message: 'Please provide a valid email.')]
            public function store(string $email): array {
                return ['email' => $email];
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };

        $response = $this->post($service, ['email' => 'bad']);
        $response->assertStatus(422)
            ->assertBodyContains('Please provide a valid email.');
    }

    public function testMissingParamCustomMessage() {
        $param = new RequestParameter('token', ParamType::STRING);
        $param->setMessage('Authentication token is required.');

        $result = ErrorResponse::missingParams([$param]);
        $json = $result['json'];

        $this->assertEquals('Authentication token is required.', $json->get('more-info')->get('errors')->get('token'));
    }

    public function testMissingParamDefaultMessage() {
        $param = new RequestParameter('name', ParamType::STRING);

        $result = ErrorResponse::missingParams([$param]);
        $json = $result['json'];

        $this->assertEquals("Required parameter 'name' is missing.", $json->get('more-info')->get('errors')->get('name'));
    }

    public function testStringParamsStillWork() {
        // Backward compat: passing string names still works
        $result = ErrorResponse::invalidParams(['field1', 'field2']);
        $json = $result['json'];
        $errors = $json->get('more-info')->get('errors');

        $this->assertEquals("Invalid value for parameter 'field1'.", $errors->get('field1'));
        $this->assertEquals("Invalid value for parameter 'field2'.", $errors->get('field2'));
    }
}
