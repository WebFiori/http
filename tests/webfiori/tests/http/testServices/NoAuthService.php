<?php

namespace webfiori\tests\http\testServices;

use webfiori\http\AbstractWebService;
use webfiori\http\RequestMethod;
/**
 * Description of NoAuthService
 *
 * @author Ibrahim
 */
class NoAuthService extends AbstractWebService {
    public function __construct() {
        parent::__construct('ok-service');
        $this->setIsAuthRequired(false);
        $this->addRequestMethod(RequestMethod::GET);
    }
    public function isAuthorized() {
        return false;
    }

    public function processRequest() {
        $this->sendResponse('You are authorized.');
    }

}
