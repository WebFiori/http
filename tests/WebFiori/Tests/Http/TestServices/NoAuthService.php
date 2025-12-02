<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\RequestMethod;
/**
 * Description of NoAuthService
 *
 * @author Ibrahim
 */
class NoAuthService extends WebService {
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
