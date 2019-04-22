<?php
namespace restEasy\tests;
use restEasy\RequestParameter;
use restEasy\APIAction;
use restEasy\WebAPI;
/**
 * Description of SampleAPI
 *
 * @author Eng.Ibrahim
 */
class SampleAPI extends WebAPI{
    //put your code here
    public function isAuthorized() {
        return true;
    }

    public function processRequest() {
        
    }

}
