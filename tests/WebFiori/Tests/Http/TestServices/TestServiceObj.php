<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\AbstractWebService;
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
        return parent::isAuthorized();
    }

    public function processRequest() {
        
    }

}
