<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebServicesManager;

// Create and configure the services manager
$manager = new WebServicesManager();
$manager->setVersion('1.0.0');
$manager->setDescription('Complete Object Mapping API Examples');

// Auto-discover and register all services
$manager->autoDiscoverServices();

// Process the incoming request
$manager->process();
