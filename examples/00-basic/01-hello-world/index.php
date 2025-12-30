<?php

use WebFiori\Http\WebServicesManager;

require_once '../../../vendor/autoload.php';

// Create and configure the services manager
$manager = new WebServicesManager();
$manager->setVersion('1.0.0');
$manager->setDescription('Hello World API Example');

// Auto-discover and register services
$manager->autoDiscoverServices();

// Process the incoming request
$manager->process();
