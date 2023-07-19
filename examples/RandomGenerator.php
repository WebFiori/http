<?php

require 'loader.php';

use GetRandomService;
use webfiori\http\WebServicesManager;

class RandomGenerator extends WebServicesManager {
    public function __construct() {
        parent::__construct();
        $this->addService(new GetRandomService());
    }
}

$manager = new RandomGenerator();
$manager->process();
