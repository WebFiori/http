<?php

require_once '../../../vendor/autoload.php';
require_once 'TaskService.php';

use WebFiori\Http\WebServicesManager;

$manager = new WebServicesManager();
$manager->addService(new TaskService());
$manager->process();
