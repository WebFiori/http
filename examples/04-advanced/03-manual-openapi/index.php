<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebServicesManager;

// Create and configure the services manager
$manager = new WebServicesManager();
$manager->setVersion('1.0.0');
$manager->setDescription('OpenAPI Documentation Example');

// Auto-discover and register all services
$manager->autoDiscoverServices();

// Process the incoming request
$manager->process();
