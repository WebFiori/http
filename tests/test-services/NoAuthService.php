<?php

namespace restEasy\tests;

use webfiori\restEasy\AbstractWebService;
/**
 * Description of NoAuthService
 *
 * @author Ibrahim
 */
class NoAuthService extends AbstractWebService {
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
