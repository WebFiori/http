<?php

require 'loader.php';
require 'HelloWorldService.php';
require 'GetRandomService.php';
require 'HelloWithAuthService.php';
require 'CompleteApiDemo.php';
require 'ProductController.php';
require 'AuthenticatedController.php';
require 'UserController.php';
require 'AuthTestService.php';

use HelloWorldService;
use GetRandomService;
use HelloWithAuthService;
use WebFiori\Http\WebServicesManager;
use WebFiori\Http\SecurityContext;

// Set up authentication context
SecurityContext::setCurrentUser(['id' => 1, 'name' => 'Demo User']);
SecurityContext::setRoles(['USER', 'ADMIN']);
SecurityContext::setCurrentUser(['id' => 1, 'name' => 'Demo User']);
SecurityContext::setRoles(['USER', 'ADMIN']);
SecurityContext::setAuthorities(['USER_CREATE', 'USER_UPDATE', 'USER_DELETE']);

$manager = new WebServicesManager();
$manager->addService(new HelloWorldService());
$manager->addService(new GetRandomService());
$manager->addService(new HelloWithAuthService());
$manager->addService(new CompleteApiDemo());
$manager->addService(new ProductController());
$manager->addService(new AuthenticatedController());
$manager->addService(new UserController());
$manager->addService(new AuthTestService());
$manager->process();
