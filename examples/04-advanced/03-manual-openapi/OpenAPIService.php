<?php

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\RequestMethod;

#[RestController('openapi', 'Returns OpenAPI 3.1.0 specification for this API')]
#[AllowAnonymous]
class OpenAPIService extends WebService {
    
    public function __construct() {
        parent::__construct();
        $this->setRequestMethods([RequestMethod::GET]);
    }
    
    public function processRequest() {
        $openApiObj = $this->getManager()->toOpenAPI();
        $info = $openApiObj->getInfo();
        $info->setTermsOfService('https://example.com/terms');
        $this->send('application/json', $openApiObj->toJSON(), 200);
    }
}
