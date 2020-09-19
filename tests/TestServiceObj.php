<?php

namespace restEasy\tests;

use webfiori\restEasy\AbstractWebService;
/**
 * Description of TestServiceObj
 *
 * @author Ibrahim
 */
class TestServiceObj extends AbstractWebService {
    public function __construct($name) {
        parent::__construct($name);
    }
    //put your code here
    public function isAuthorized() {
        
    }

    public function processRequest($inputs) {
        
    }

}
