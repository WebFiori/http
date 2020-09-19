<?php

namespace restEasy\tests;

use webfiori\restEasy\WebService;
/**
 * Description of NoAuthService
 *
 * @author Ibrahim
 */
class NoAuthService extends WebService {
    public function __construct() {
        parent::__construct('ok-service');
        $this->setIsAuthRequred(false);
        $this->addRequestMethod('get');
    }
    public function isAuthorized() {
        return false;
    }

    public function processRequest($inputs) {
        $this->sendResponse('You are auuthorized.');
    }

}
