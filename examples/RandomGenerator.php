<?php

require 'loader.php';

use GetRandomService;
use WebFiori\Http\WebServicesManager;

class RandomGenerator extends WebServicesManager {
    public function __construct() {
        parent::__construct();
        $this->addService(new GetRandomService());
    }
}

$manager = new RandomGenerator();
$manager->process();
