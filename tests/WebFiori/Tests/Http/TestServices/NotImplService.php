<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\AbstractWebService;
use WebFiori\Http\RequestMethod;

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
