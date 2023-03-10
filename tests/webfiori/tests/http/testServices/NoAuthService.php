<?php

namespace webfiori\tests\http\testServices;

use webfiori\http\AbstractWebService;
/**
 * Description of NoAuthService
 *
 * @author Ibrahim
 */
class NoAuthService extends AbstractWebService {
    public function __construct() {
        parent::__construct('ok-service');
        $this->setIsAuthRequired(false);
        $this->addRequestMethod('get');
    }
    public function isAuthorized() {
        return false;
    }

    public function processRequest() {
        $this->sendResponse('You are authorized.');
    }

}
