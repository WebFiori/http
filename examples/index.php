<?php

require 'loader.php';
require 'HelloWorldService.php';

use HelloWorldService;
use WebFiori\Http\WebServicesManager;

$manager = new WebServicesManager();
$manager->addService(new HelloWorldService());
$manager->process();
