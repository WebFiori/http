<?php

require 'loader.php';
require 'HelloWorldService.php';

use HelloWorldService;
use webfiori\http\WebServicesManager;

$manager = new WebServicesManager();
$manager->addService(new HelloWorldService());
$manager->process();
