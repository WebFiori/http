<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\OpenAPI\InfoObj;
use WebFiori\Http\OpenAPI\LicenseObj;
use WebFiori\Http\OpenAPI\ContactObj;
use WebFiori\Http\OpenAPI\OpenAPIObj;
use WebFiori\Http\OpenAPI\ServerObj;
use WebFiori\Http\OpenAPI\TagObj;
use WebFiori\Http\OpenAPI\ExternalDocObj;
use WebFiori\Http\OpenAPI\PathsObj;
use WebFiori\Http\OpenAPI\PathItemObj;
use WebFiori\Http\OpenAPI\OperationObj;
use WebFiori\Http\OpenAPI\ResponsesObj;
use WebFiori\Http\OpenAPI\ResponseObj;
use WebFiori\Http\OpenAPI\ParameterObj;
use WebFiori\Http\OpenAPI\HeaderObj;
use WebFiori\Http\OpenAPI\MediaTypeObj;
use WebFiori\Http\OpenAPI\ReferenceObj;
use WebFiori\Http\OpenAPI\SecuritySchemeObj;
use WebFiori\Http\OpenAPI\OAuthFlowObj;
use WebFiori\Http\OpenAPI\OAuthFlowsObj;
use WebFiori\Http\OpenAPI\SecurityRequirementObj;
use WebFiori\Http\OpenAPI\ComponentsObj;

class OpenAPITest extends TestCase {
    
    /**
     * @test
     */
    public function testInfoObj() {
        $info = new InfoObj('My API', '1.0.0');
        $this->assertEquals('My API', $info->getTitle());
        $this->assertEquals('1.0.0', $info->getVersion());
        
        $info->setSummary('API Summary');
        $this->assertEquals('API Summary', $info->getSummary());
        
        $info->setDescription('API Description');
        $this->assertEquals('API Description', $info->getDescription());
        
        $info->setTermsOfService('https://example.com/terms');
        $this->assertEquals('https://example.com/terms', $info->getTermsOfService());
        
        $contact = new ContactObj();
        $info->setContact($contact);
        $this->assertSame($contact, $info->getContact());
        
        $license = new LicenseObj('MIT');
        $info->setLicense($license);
        $this->assertSame($license, $info->getLicense());
        
        $json = $info->toJSON();
        $this->assertEquals('My API', $json->get('title'));
        $this->assertEquals('1.0.0', $json->get('version'));
        $this->assertEquals('API Summary', $json->get('summary'));
    }
    
    /**
     * @test
     */
    public function testLicenseObj() {
        $license = new LicenseObj('Apache 2.0');
        $this->assertEquals('Apache 2.0', $license->getName());
        
        $license->setIdentifier('Apache-2.0');
        $this->assertEquals('Apache-2.0', $license->getIdentifier());
        $this->assertNull($license->getUrl());
        
        $license->setUrl('https://www.apache.org/licenses/LICENSE-2.0.html');
        $this->assertEquals('https://www.apache.org/licenses/LICENSE-2.0.html', $license->getUrl());
        $this->assertNull($license->getIdentifier());
        
        $json = $license->toJSON();
        $this->assertEquals('Apache 2.0', $json->get('name'));
        $this->assertEquals('https://www.apache.org/licenses/LICENSE-2.0.html', $json->get('url'));
    }
    
    /**
     * @test
     */
    public function testContactObj() {
        $contact = new ContactObj();
        $this->assertNull($contact->getName());
        
        $contact->setName('API Support');
        $this->assertEquals('API Support', $contact->getName());
        
        $contact->setUrl('https://example.com/support');
        $this->assertEquals('https://example.com/support', $contact->getUrl());
        
        $contact->setEmail('support@example.com');
        $this->assertEquals('support@example.com', $contact->getEmail());
        
        $json = $contact->toJSON();
        $this->assertEquals('API Support', $json->get('name'));
        $this->assertEquals('https://example.com/support', $json->get('url'));
        $this->assertEquals('support@example.com', $json->get('email'));
    }
    
    /**
     * @test
     */
    public function testOpenAPIObj() {
        $info = new InfoObj('Test API', '2.0.0');
        $openapi = new OpenAPIObj($info);
        
        $this->assertEquals('3.1.0', $openapi->getOpenapi());
        $this->assertSame($info, $openapi->getInfo());
        
        $openapi->setOpenapi('3.0.0');
        $this->assertEquals('3.0.0', $openapi->getOpenapi());
        
        $paths = new PathsObj();
        $openapi->setPaths($paths);
        $this->assertSame($paths, $openapi->getPaths());
        
        $json = $openapi->toJSON();
        $this->assertEquals('3.0.0', $json->get('openapi'));
        $this->assertNotNull($json->get('info'));
    }
    
    /**
     * @test
     */
    public function testServerObj() {
        $server = new ServerObj('https://api.example.com');
        $this->assertEquals('https://api.example.com', $server->getUrl());
        
        $server->setDescription('Production server');
        $this->assertEquals('Production server', $server->getDescription());
        
        $json = $server->toJSON();
        $this->assertEquals('https://api.example.com', $json->get('url'));
        $this->assertEquals('Production server', $json->get('description'));
    }
    
    /**
     * @test
     */
    public function testTagObj() {
        $tag = new TagObj('users');
        $this->assertEquals('users', $tag->getName());
        
        $tag->setDescription('User operations');
        $this->assertEquals('User operations', $tag->getDescription());
        
        $externalDocs = new ExternalDocObj('https://docs.example.com');
        $tag->setExternalDocs($externalDocs);
        $this->assertSame($externalDocs, $tag->getExternalDocs());
        
        $json = $tag->toJSON();
        $this->assertEquals('users', $json->get('name'));
        $this->assertEquals('User operations', $json->get('description'));
    }
    
    /**
     * @test
     */
    public function testExternalDocObj() {
        $doc = new ExternalDocObj('https://docs.example.com');
        $this->assertEquals('https://docs.example.com', $doc->getUrl());
        
        $doc->setDescription('External documentation');
        $this->assertEquals('External documentation', $doc->getDescription());
        
        $json = $doc->toJSON();
        $this->assertEquals('https://docs.example.com', $json->get('url'));
        $this->assertEquals('External documentation', $json->get('description'));
    }
    
    /**
     * @test
     */
    public function testPathsObj() {
        $paths = new PathsObj();
        $this->assertEmpty($paths->getPaths());
        
        $pathItem = new PathItemObj();
        $paths->addPath('/users', $pathItem);
        
        $allPaths = $paths->getPaths();
        $this->assertCount(1, $allPaths);
        $this->assertSame($pathItem, $allPaths['/users']);
        
        $json = $paths->toJSON();
        $this->assertNotNull($json->get('/users'));
    }
    
    /**
     * @test
     */
    public function testPathItemObj() {
        $pathItem = new PathItemObj();
        $this->assertNull($pathItem->getGet());
        $this->assertNull($pathItem->getPost());
        
        $responses = new ResponsesObj();
        $responses->addResponse('200', 'Success');
        
        $getOp = new OperationObj($responses);
        $pathItem->setGet($getOp);
        $this->assertSame($getOp, $pathItem->getGet());
        
        $postOp = new OperationObj($responses);
        $pathItem->setPost($postOp);
        $this->assertSame($postOp, $pathItem->getPost());
        
        $putOp = new OperationObj($responses);
        $pathItem->setPut($putOp);
        $this->assertSame($putOp, $pathItem->getPut());
        
        $deleteOp = new OperationObj($responses);
        $pathItem->setDelete($deleteOp);
        $this->assertSame($deleteOp, $pathItem->getDelete());
        
        $patchOp = new OperationObj($responses);
        $pathItem->setPatch($patchOp);
        $this->assertSame($patchOp, $pathItem->getPatch());
        
        $json = $pathItem->toJSON();
        $this->assertNotNull($json->get('get'));
        $this->assertNotNull($json->get('post'));
        $this->assertNotNull($json->get('put'));
        $this->assertNotNull($json->get('delete'));
        $this->assertNotNull($json->get('patch'));
    }
    
    /**
     * @test
     */
    public function testOperationObj() {
        $responses = new ResponsesObj();
        $responses->addResponse('200', 'Success');
        
        $operation = new OperationObj($responses);
        $this->assertSame($responses, $operation->getResponses());
        
        $newResponses = new ResponsesObj();
        $operation->setResponses($newResponses);
        $this->assertSame($newResponses, $operation->getResponses());
        
        $json = $operation->toJSON();
        $this->assertNotNull($json->get('responses'));
    }
    
    /**
     * @test
     */
    public function testResponsesObj() {
        $responses = new ResponsesObj();
        $this->assertEmpty($responses->getResponses());
        
        $responses->addResponse('200', 'Success');
        $responses->addResponse('404', 'Not found');
        
        $allResponses = $responses->getResponses();
        $this->assertCount(2, $allResponses);
        $this->assertInstanceOf(ResponseObj::class, $allResponses['200']);
        $this->assertEquals('Success', $allResponses['200']->getDescription());
        
        $json = $responses->toJSON();
        $this->assertNotNull($json->get('200'));
        $this->assertNotNull($json->get('404'));
    }
    
    /**
     * @test
     */
    public function testResponseObj() {
        $response = new ResponseObj('Operation successful');
        $this->assertEquals('Operation successful', $response->getDescription());
        
        $response->setDescription('Updated description');
        $this->assertEquals('Updated description', $response->getDescription());
        
        $json = $response->toJSON();
        $this->assertEquals('Updated description', $json->get('description'));
    }
    
    /**
     * @test
     */
    public function testParameterObj() {
        $param = new ParameterObj('userId', 'path');
        $this->assertEquals('userId', $param->getName());
        $this->assertEquals('path', $param->getIn());
        
        $param->setDescription('User ID parameter');
        $this->assertEquals('User ID parameter', $param->getDescription());
        
        $param->setRequired(true);
        $this->assertTrue($param->getRequired());
        
        $param->setDeprecated(true);
        $this->assertTrue($param->getDeprecated());
        
        $param->setAllowEmptyValue(true);
        $this->assertTrue($param->getAllowEmptyValue());
        
        $param->setStyle('simple');
        $this->assertEquals('simple', $param->getStyle());
        
        $param->setExplode(false);
        $this->assertFalse($param->getExplode());
        
        $param->setAllowReserved(true);
        $this->assertTrue($param->getAllowReserved());
        
        $param->setSchema(['type' => 'integer']);
        $this->assertEquals(['type' => 'integer'], $param->getSchema());
        
        $param->setExample(123);
        $this->assertEquals(123, $param->getExample());
        
        $param->setExamples(['example1' => ['value' => 123]]);
        $this->assertEquals(['example1' => ['value' => 123]], $param->getExamples());
        
        $json = $param->toJSON();
        $this->assertEquals('userId', $json->get('name'));
        $this->assertEquals('path', $json->get('in'));
        $this->assertEquals('User ID parameter', $json->get('description'));
    }
    
    /**
     * @test
     */
    public function testHeaderObj() {
        $header = new HeaderObj();
        $this->assertNull($header->getDescription());
        
        $header->setDescription('Custom header');
        $this->assertEquals('Custom header', $header->getDescription());
        
        $header->setRequired(true);
        $this->assertTrue($header->getRequired());
        
        $header->setDeprecated(true);
        $this->assertTrue($header->getDeprecated());
        
        $header->setStyle('simple');
        $this->assertEquals('simple', $header->getStyle());
        
        $header->setExplode(true);
        $this->assertTrue($header->getExplode());
        
        $header->setSchema(['type' => 'string']);
        $this->assertEquals(['type' => 'string'], $header->getSchema());
        
        $header->setExample('example-value');
        $this->assertEquals('example-value', $header->getExample());
        
        $header->setExamples(['ex1' => ['value' => 'test']]);
        $this->assertEquals(['ex1' => ['value' => 'test']], $header->getExamples());
        
        $json = $header->toJSON();
        $this->assertEquals('Custom header', $json->get('description'));
    }
    
    /**
     * @test
     */
    public function testMediaTypeObj() {
        $mediaType = new MediaTypeObj();
        $this->assertNull($mediaType->getSchema());
        
        $mediaType->setSchema(['type' => 'object']);
        $this->assertEquals(['type' => 'object'], $mediaType->getSchema());
        
        $json = $mediaType->toJSON();
        $this->assertNotNull($json->get('schema'));
    }
    
    /**
     * @test
     */
    public function testReferenceObj() {
        $ref = new ReferenceObj('#/components/schemas/User');
        $this->assertEquals('#/components/schemas/User', $ref->getRef());
        
        $ref->setSummary('User reference');
        $this->assertEquals('User reference', $ref->getSummary());
        
        $ref->setDescription('Reference to User schema');
        $this->assertEquals('Reference to User schema', $ref->getDescription());
        
        $json = $ref->toJSON();
        $this->assertEquals('#/components/schemas/User', $json->get('$ref'));
        $this->assertEquals('User reference', $json->get('summary'));
    }
    
    /**
     * @test
     */
    public function testSecuritySchemeObj() {
        $scheme = new SecuritySchemeObj('http');
        $this->assertEquals('http', $scheme->getType());
        
        $scheme->setDescription('HTTP Basic Auth');
        $this->assertEquals('HTTP Basic Auth', $scheme->getDescription());
        
        $scheme->setName('Authorization');
        $this->assertEquals('Authorization', $scheme->getName());
        
        $scheme->setIn('header');
        $this->assertEquals('header', $scheme->getIn());
        
        $scheme->setScheme('basic');
        $this->assertEquals('basic', $scheme->getScheme());
        
        $scheme->setBearerFormat('JWT');
        $this->assertEquals('JWT', $scheme->getBearerFormat());
        
        $flows = new OAuthFlowsObj();
        $scheme->setFlows($flows);
        $this->assertSame($flows, $scheme->getFlows());
        
        $scheme->setOpenIdConnectUrl('https://example.com/.well-known/openid-configuration');
        $this->assertEquals('https://example.com/.well-known/openid-configuration', $scheme->getOpenIdConnectUrl());
        
        $json = $scheme->toJSON();
        $this->assertEquals('http', $json->get('type'));
        $this->assertEquals('HTTP Basic Auth', $json->get('description'));
    }
    
    /**
     * @test
     */
    public function testOAuthFlowObj() {
        $flow = new OAuthFlowObj();
        $this->assertEmpty($flow->getScopes());
        
        $flow->setAuthorizationUrl('https://example.com/oauth/authorize');
        $this->assertEquals('https://example.com/oauth/authorize', $flow->getAuthorizationUrl());
        
        $flow->setTokenUrl('https://example.com/oauth/token');
        $this->assertEquals('https://example.com/oauth/token', $flow->getTokenUrl());
        
        $flow->setRefreshUrl('https://example.com/oauth/refresh');
        $this->assertEquals('https://example.com/oauth/refresh', $flow->getRefreshUrl());
        
        $flow->addScope('read', 'Read access');
        $flow->addScope('write', 'Write access');
        
        $scopes = $flow->getScopes();
        $this->assertCount(2, $scopes);
        $this->assertEquals('Read access', $scopes['read']);
        
        $json = $flow->toJSON();
        $this->assertNotNull($json->get('scopes'));
    }
    
    /**
     * @test
     */
    public function testOAuthFlowsObj() {
        $flows = new OAuthFlowsObj();
        $this->assertNull($flows->getImplicit());
        
        $implicit = new OAuthFlowObj();
        $flows->setImplicit($implicit);
        $this->assertSame($implicit, $flows->getImplicit());
        
        $password = new OAuthFlowObj();
        $flows->setPassword($password);
        $this->assertSame($password, $flows->getPassword());
        
        $clientCredentials = new OAuthFlowObj();
        $flows->setClientCredentials($clientCredentials);
        $this->assertSame($clientCredentials, $flows->getClientCredentials());
        
        $authCode = new OAuthFlowObj();
        $flows->setAuthorizationCode($authCode);
        $this->assertSame($authCode, $flows->getAuthorizationCode());
        
        $json = $flows->toJSON();
        $this->assertNotNull($json->get('implicit'));
        $this->assertNotNull($json->get('password'));
    }
    
    /**
     * @test
     */
    public function testSecurityRequirementObj() {
        $requirement = new SecurityRequirementObj();
        $this->assertEmpty($requirement->getRequirements());
        
        $requirement->addRequirement('api_key', []);
        $requirement->addRequirement('oauth2', ['read', 'write']);
        
        $reqs = $requirement->getRequirements();
        $this->assertCount(2, $reqs);
        $this->assertEmpty($reqs['api_key']);
        $this->assertEquals(['read', 'write'], $reqs['oauth2']);
        
        $json = $requirement->toJSON();
        $this->assertNotNull($json->get('api_key'));
        $this->assertNotNull($json->get('oauth2'));
    }
    
    /**
     * @test
     */
    public function testComponentsObj() {
        $components = new ComponentsObj();
        $this->assertEmpty($components->getSchemas());
        $this->assertEmpty($components->getSecuritySchemes());
        
        $components->addSchema('User', ['type' => 'object']);
        $schemas = $components->getSchemas();
        $this->assertCount(1, $schemas);
        $this->assertEquals(['type' => 'object'], $schemas['User']);
        
        $securityScheme = new SecuritySchemeObj('http');
        $components->addSecurityScheme('basicAuth', $securityScheme);
        $schemes = $components->getSecuritySchemes();
        $this->assertCount(1, $schemes);
        $this->assertSame($securityScheme, $schemes['basicAuth']);
        
        $json = $components->toJSON();
        $this->assertNotNull($json->get('schemas'));
        $this->assertNotNull($json->get('securitySchemes'));
    }
}
