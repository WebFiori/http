<?php

require 'loader.php';

use WebFiori\Http\AbstractWebService;
use WebFiori\Http\RequestParameter;

class GetRandomService extends AbstractWebService {
    public function __construct() {
        parent::__construct('get-random-number');
        $this->setRequestMethods(['get', 'post']);

        $this->addParameter(new RequestParameter('min', 'integer', true));
        $this->addParameter(new RequestParameter('max', 'integer', true));
    }

    public function isAuthorized() {
    }

    public function processRequest() {
        $max = $this->getParamVal('max');
        $min = $this->getParamVal('min');

        if ($max !== null && $min !== null) {
            $random = rand($min, $max);
        } else {
            $random = rand();
        }
        $this->sendResponse($random);
    }
}
