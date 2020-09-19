<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace restEasy\tests;
use webfiori\restEasy\AbstractWebService;
use webfiori\restEasy\RequestParameter;

/**
 * Description of AbstractNumbersService
 *
 * @author Ibrahim
 */
abstract class AbstractNumbersService extends AbstractWebService {
    public function __construct($name) {
        parent::__construct($name);
        $this->addParameter(new RequestParameter('pass','string'));
    }
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









