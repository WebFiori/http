<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APIFilter;
use WebFiori\Http\APITestCase;
use WebFiori\Http\OpenAPI\Schema;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\Request;
use WebFiori\Http\RequestParameter;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AllowedValuesPatternService;

/**
 * Tests for allowed-values and pattern validation on RequestParameter.
 * 
 * Covers: RequestParameter, APIFilter, RequestParam attribute, OpenAPI Schema.
 * 
 * @see https://github.com/WebFiori/http/issues/114
 */
class AllowedValuesAndPatternTest extends APITestCase {

    // =========================================================================
    // RequestParameter unit tests
    // =========================================================================

    public function testSetAllowedValues() {
        $param = new RequestParameter('status', ParamType::STRING);
        $param->setAllowedValues(['active', 'inactive']);
        $this->assertEquals(['active', 'inactive'], $param->getAllowedValues());
    }

    public function testGetAllowedValuesDefaultEmpty() {
        $param = new RequestParameter('name', ParamType::STRING);
        $this->assertEquals([], $param->getAllowedValues());
    }

    public function testSetPatternValid() {
        $param = new RequestParameter('phone', ParamType::STRING);
        $this->assertTrue($param->setPattern('/^\+[0-9]+$/'));
        $this->assertEquals('/^\+[0-9]+$/', $param->getPattern());
    }

    public function testSetPatternInvalid() {
        $param = new RequestParameter('phone', ParamType::STRING);
        $this->assertFalse($param->setPattern('/invalid[/'));
        $this->assertNull($param->getPattern());
    }

    public function testGetPatternDefaultNull() {
        $param = new RequestParameter('name', ParamType::STRING);
        $this->assertNull($param->getPattern());
    }

    public function testCreateWithAllowedValues() {
        $param = RequestParameter::create([
            ParamOption::NAME => 'color',
            ParamOption::TYPE => ParamType::STRING,
            ParamOption::ALLOWED_VALUES => ['red', 'green', 'blue']
        ]);
        $this->assertEquals(['red', 'green', 'blue'], $param->getAllowedValues());
    }

    public function testCreateWithPattern() {
        $param = RequestParameter::create([
            ParamOption::NAME => 'zip',
            ParamOption::TYPE => ParamType::STRING,
            ParamOption::PATTERN => '/^[0-9]{5}$/'
        ]);
        $this->assertEquals('/^[0-9]{5}$/', $param->getPattern());
    }

    // =========================================================================
    // APIFilter — allowed-values (GET/form-encoded)
    // =========================================================================

    public function testAllowedValuesAccepted() {
        $filter = new APIFilter();
        $param = new RequestParameter('status', ParamType::STRING);
        $param->setAllowedValues(['active', 'inactive', 'pending']);
        $filter->addRequestParameter($param);

        $_GET = ['status' => 'active'];
        $filter->filterGET();
        $this->assertEquals('active', $filter->getInputs()['status']);
    }

    public function testAllowedValuesRejected() {
        $filter = new APIFilter();
        $param = new RequestParameter('status', ParamType::STRING);
        $param->setAllowedValues(['active', 'inactive', 'pending']);
        $filter->addRequestParameter($param);

        $_GET = ['status' => 'deleted'];
        $filter->filterGET();
        $this->assertEquals(APIFilter::INVALID, $filter->getInputs()['status']);
    }

    public function testAllowedValuesOnIntParam() {
        $filter = new APIFilter();
        $param = new RequestParameter('priority', ParamType::INT);
        $param->setAllowedValues([1, 2, 3]);
        $filter->addRequestParameter($param);

        $_GET = ['priority' => '2'];
        $filter->filterGET();
        $this->assertEquals(2, $filter->getInputs()['priority']);
    }

    public function testAllowedValuesOnIntParamRejected() {
        $filter = new APIFilter();
        $param = new RequestParameter('priority', ParamType::INT);
        $param->setAllowedValues([1, 2, 3]);
        $filter->addRequestParameter($param);

        $_GET = ['priority' => '5'];
        $filter->filterGET();
        $this->assertEquals(APIFilter::INVALID, $filter->getInputs()['priority']);
    }

    public function testAllowedValuesEmptyArrayNoRestriction() {
        $filter = new APIFilter();
        $param = new RequestParameter('name', ParamType::STRING);
        $param->setAllowedValues([]);
        $filter->addRequestParameter($param);

        $_GET = ['name' => 'anything'];
        $filter->filterGET();
        $this->assertEquals('anything', $filter->getInputs()['name']);
    }

    // =========================================================================
    // APIFilter — pattern (GET/form-encoded)
    // =========================================================================

    public function testPatternAccepted() {
        $filter = new APIFilter();
        $param = new RequestParameter('zip', ParamType::STRING);
        $param->setPattern('/^[0-9]{5}$/');
        $filter->addRequestParameter($param);

        $_GET = ['zip' => '12345'];
        $filter->filterGET();
        $this->assertEquals('12345', $filter->getInputs()['zip']);
    }

    public function testPatternRejected() {
        $filter = new APIFilter();
        $param = new RequestParameter('zip', ParamType::STRING);
        $param->setPattern('/^[0-9]{5}$/');
        $filter->addRequestParameter($param);

        $_GET = ['zip' => 'abcde'];
        $filter->filterGET();
        $this->assertEquals(APIFilter::INVALID, $filter->getInputs()['zip']);
    }

    public function testPatternPartialMatchRejected() {
        $filter = new APIFilter();
        $param = new RequestParameter('code', ParamType::STRING);
        $param->setPattern('/^[A-Z]{3}$/');
        $filter->addRequestParameter($param);

        $_GET = ['code' => 'AB'];
        $filter->filterGET();
        $this->assertEquals(APIFilter::INVALID, $filter->getInputs()['code']);
    }

    public function testPatternWithNoPatternSetPasses() {
        $filter = new APIFilter();
        $param = new RequestParameter('name', ParamType::STRING);
        $filter->addRequestParameter($param);

        $_GET = ['name' => 'anything goes'];
        $filter->filterGET();
        $this->assertEquals('anything goes', $filter->getInputs()['name']);
    }

    // =========================================================================
    // APIFilter — both constraints together
    // =========================================================================

    public function testBothAllowedValuesAndPatternPass() {
        $filter = new APIFilter();
        $param = new RequestParameter('code', ParamType::STRING);
        $param->setAllowedValues(['ABC', 'DEF', 'GHI']);
        $param->setPattern('/^[A-Z]{3}$/');
        $filter->addRequestParameter($param);

        $_GET = ['code' => 'ABC'];
        $filter->filterGET();
        $this->assertEquals('ABC', $filter->getInputs()['code']);
    }

    public function testAllowedValuesPassButPatternFails() {
        $filter = new APIFilter();
        $param = new RequestParameter('code', ParamType::STRING);
        $param->setAllowedValues(['abc', 'def']);
        $param->setPattern('/^[A-Z]{3}$/');
        $filter->addRequestParameter($param);

        // 'abc' is in allowed values but doesn't match uppercase pattern
        $_GET = ['code' => 'abc'];
        $filter->filterGET();
        $this->assertEquals(APIFilter::INVALID, $filter->getInputs()['code']);
    }

    public function testPatternPassesButNotInAllowedValues() {
        $filter = new APIFilter();
        $param = new RequestParameter('code', ParamType::STRING);
        $param->setAllowedValues(['ABC', 'DEF']);
        $param->setPattern('/^[A-Z]{3}$/');
        $filter->addRequestParameter($param);

        // 'GHI' matches pattern but not in allowed values
        $_GET = ['code' => 'GHI'];
        $filter->filterGET();
        $this->assertEquals(APIFilter::INVALID, $filter->getInputs()['code']);
    }

    // =========================================================================
    // APIFilter — JSON body path
    // =========================================================================

    public function testAllowedValuesWithJsonBody() {
        $filter = new APIFilter();
        $param = new RequestParameter('status', ParamType::STRING);
        $param->setAllowedValues(['active', 'inactive']);
        $filter->addRequestParameter($param);

        $jsonFile = sys_get_temp_dir() . '/allowed_values_test.json';
        file_put_contents($jsonFile, json_encode(['status' => 'active']));

        $filter->setInputStream($jsonFile);
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $filter->filterPOST();

        $inputs = $filter->getInputs();
        $this->assertEquals('active', $inputs->get('status'));
        @unlink($jsonFile);
    }

    public function testAllowedValuesRejectedWithJsonBody() {
        $filter = new APIFilter();
        $param = new RequestParameter('status', ParamType::STRING);
        $param->setAllowedValues(['active', 'inactive']);
        $filter->addRequestParameter($param);

        $jsonFile = sys_get_temp_dir() . '/allowed_values_test2.json';
        file_put_contents($jsonFile, json_encode(['status' => 'deleted']));

        $filter->setInputStream($jsonFile);
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $filter->filterPOST();

        $inputs = $filter->getInputs();
        $this->assertNull($inputs->get('status'));
        @unlink($jsonFile);
    }

    public function testPatternWithJsonBody() {
        $filter = new APIFilter();
        $param = new RequestParameter('phone', ParamType::STRING);
        $param->setPattern('/^\+[0-9]{10,15}$/');
        $filter->addRequestParameter($param);

        $jsonFile = sys_get_temp_dir() . '/pattern_test.json';
        file_put_contents($jsonFile, json_encode(['phone' => '+1234567890123']));

        $filter->setInputStream($jsonFile);
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $filter->filterPOST();

        $inputs = $filter->getInputs();
        $this->assertEquals('+1234567890123', $inputs->get('phone'));
        @unlink($jsonFile);
    }

    public function testPatternRejectedWithJsonBody() {
        $filter = new APIFilter();
        $param = new RequestParameter('phone', ParamType::STRING);
        $param->setPattern('/^\+[0-9]{10,15}$/');
        $filter->addRequestParameter($param);

        $jsonFile = sys_get_temp_dir() . '/pattern_test2.json';
        file_put_contents($jsonFile, json_encode(['phone' => 'not-a-phone']));

        $filter->setInputStream($jsonFile);
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $filter->filterPOST();

        $inputs = $filter->getInputs();
        $this->assertNull($inputs->get('phone'));
        @unlink($jsonFile);
    }

    // =========================================================================
    // Attribute-based service integration tests
    // =========================================================================

    public function testAllowedValuesViaAttribute() {
        $manager = new WebServicesManager();
        $manager->addService(new AllowedValuesPatternService());

        $output = $this->getRequest($manager, 'allowed-values-pattern-service', [
            'status' => 'active',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('active', $response['status']);
    }

    public function testAllowedValuesViaAttributeRejected() {
        $manager = new WebServicesManager();
        $manager->addService(new AllowedValuesPatternService());

        $output = $this->getRequest($manager, 'allowed-values-pattern-service', [
            'status' => 'deleted',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }

    public function testPatternViaAttribute() {
        $manager = new WebServicesManager();
        $manager->addService(new AllowedValuesPatternService());

        $output = $this->postRequest($manager, 'allowed-values-pattern-service', [
            'phone' => '%2B1234567890123',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('+1234567890123', $response['phone']);
    }

    public function testPatternViaAttributeRejected() {
        $manager = new WebServicesManager();
        $manager->addService(new AllowedValuesPatternService());

        $output = $this->postRequest($manager, 'allowed-values-pattern-service', [
            'phone' => 'invalid',
        ]);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }

    // =========================================================================
    // OpenAPI Schema tests
    // =========================================================================

    public function testSchemaWithAllowedValues() {
        $param = new RequestParameter('status', ParamType::STRING);
        $param->setAllowedValues(['active', 'inactive', 'pending']);

        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();

        $this->assertEquals(['active', 'inactive', 'pending'], $json->get('enum'));
    }

    public function testSchemaWithPattern() {
        $param = new RequestParameter('zip', ParamType::STRING);
        $param->setPattern('/^[0-9]{5}$/');

        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();

        // Pattern should be without PHP delimiters
        $this->assertEquals('^[0-9]{5}$', $json->get('pattern'));
    }

    public function testSchemaWithBothConstraints() {
        $param = new RequestParameter('code', ParamType::STRING);
        $param->setAllowedValues(['ABC', 'DEF']);
        $param->setPattern('/^[A-Z]{3}$/');

        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();

        $this->assertEquals(['ABC', 'DEF'], $json->get('enum'));
        $this->assertEquals('^[A-Z]{3}$', $json->get('pattern'));
    }

    public function testSchemaWithoutConstraintsHasNoEnumOrPattern() {
        $param = new RequestParameter('name', ParamType::STRING);

        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();

        $this->assertFalse($json->hasKey('enum'));
        $this->assertFalse($json->hasKey('pattern'));
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testAllowedValuesWithDefaultFallback() {
        $filter = new APIFilter();
        $param = new RequestParameter('status', ParamType::STRING);
        $param->setAllowedValues(['active', 'inactive']);
        $param->setIsOptional(true);
        $param->setDefault('active');
        $filter->addRequestParameter($param);

        // Value not in allowed set, but has default
        $_GET = ['status' => 'deleted'];
        $filter->filterGET();
        // Should fall back to default since INVALID + default = default
        $this->assertEquals('active', $filter->getInputs()['status']);
    }

    public function testPatternOnEmailType() {
        $filter = new APIFilter();
        $param = new RequestParameter('email', ParamType::EMAIL);
        $param->setPattern('/@company\.com$/');
        $filter->addRequestParameter($param);

        // Valid email but wrong domain
        $_GET = ['email' => 'user@other.com'];
        $filter->filterGET();
        $this->assertEquals(APIFilter::INVALID, $filter->getInputs()['email']);
    }

    public function testPatternOnEmailTypeAccepted() {
        $filter = new APIFilter();
        $param = new RequestParameter('email', ParamType::EMAIL);
        $param->setPattern('/@company\.com$/');
        $filter->addRequestParameter($param);

        $_GET = ['email' => 'user@company.com'];
        $filter->filterGET();
        $this->assertEquals('user@company.com', $filter->getInputs()['email']);
    }
}
