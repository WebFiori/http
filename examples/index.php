<?php

require 'loader.php';
require 'HelloWorldService.php';
require 'GetRandomService.php';
require 'HelloWithAuthService.php';

use HelloWorldService;
use GetRandomService;
use HelloWithAuthService;
use WebFiori\Http\WebServicesManager;

$manager = new WebServicesManager();
$manager->addService(new HelloWorldService());
$manager->addService(new GetRandomService());
$manager->addService(new HelloWithAuthService());
$manager->process();
