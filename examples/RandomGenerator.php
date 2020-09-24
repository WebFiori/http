<?php
require 'loader.php';

use webfiori\restEasy\WebServicesManager;
use GetRandomService;

class RandomGenerator extends WebServicesManager {
    public function __construct() {
        parent::__construct();
        $this->addService(new GetRandomService());
    }
}

$manager = new RandomGenerator();
$manager->process();