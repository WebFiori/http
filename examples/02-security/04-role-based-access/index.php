<?php

require_once 'PublicService.php';
require_once 'UserService.php';
require_once 'AdminService.php';
require_once 'UserManagerService.php';

use WebFiori\Http\WebServicesManager;

// Create and configure the services manager
$manager = new WebServicesManager();
$manager->setVersion('1.0.0');
$manager->setDescription('Role-Based Access Control API - Multiple Services');

// Register all RBAC services
$manager->addService(new PublicService());
$manager->addService(new UserService());
$manager->addService(new AdminService());
$manager->addService(new UserManagerService());

// Process the incoming request
$manager->process();
