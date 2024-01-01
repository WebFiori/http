<?php

namespace webfiori\tests\http\testServices;

use webfiori\http\AbstractWebService;
use webfiori\http\RequestMethod;

/**
 * Description of NotImplService
 *
 * @author Ibrahim
 */
class NotImplService extends AbstractWebService {
    public function __construct() {
        parent::__construct('not-implemented');
        $this->addRequestMethod(RequestMethod::POST);
    }
    public function isAuthorized() {
        
    }

    public function processRequest() {
        $this->getManager()->serviceNotImplemented();
    }

}
