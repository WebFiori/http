<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\RequestMethod;

/**
 * Description of NotImplService
 *
 * @author Ibrahim
 */
class NotImplService extends WebService {
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
