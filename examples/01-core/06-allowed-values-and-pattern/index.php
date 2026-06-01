<?php

require_once '../../../vendor/autoload.php';
require_once 'OrderService.php';

use WebFiori\Http\WebServicesManager;

$manager = new WebServicesManager();
$manager->addService(new OrderService());
$manager->process();
