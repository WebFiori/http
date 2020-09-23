<?php
require 'loader.php';
require 'HelloWorldService.php';

use webfiori\restEasy\WebServicesManager;
use HelloWorldService;

$manager = new WebServicesManager();
$manager->addService(new HelloWorldService());
$manager->process();
