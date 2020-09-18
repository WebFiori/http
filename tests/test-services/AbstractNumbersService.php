<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace restEasy\tests;
use webfiori\restEasy\WebService;

/**
 * Description of AbstractNumbersService
 *
 * @author Ibrahim
 */
abstract class AbstractNumbersService extends WebService {
    //put your code here
    public function isAuthorized() {
        $inputs = $this->getInputs();
        $pass = isset($inputs['pass']) ? $inputs['pass'] : null;

        if ($pass == null) {
            $pass = isset($inputs['pass']) ? $inputs['pass'] : null;
        }

        if ($pass == '123') {
            return true;
        }

        return false;
    }

}
