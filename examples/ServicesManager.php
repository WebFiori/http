<?php
require 'loader.php';
require 'HelloWorldService.php';

use webfiori\http\WebServicesManager;
use HelloWorldService;

$manager = new WebServicesManager();
$manager->addService(new HelloWorldService());
$manager->process();
