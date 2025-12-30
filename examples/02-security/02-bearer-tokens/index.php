<?php

use WebFiori\Http\WebServicesManager;

require_once '../../../vendor/autoload.php';

// Create and configure the services manager
$manager = new WebServicesManager();
$manager->setVersion('1.0.0');
$manager->setDescription('Bearer Token Authentication API');

// Auto-discover and register services
$manager->autoDiscoverServices();

// Process the incoming request
$manager->process();
