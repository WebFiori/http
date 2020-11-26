<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once '../vendor/webfiori/jsonx/src/JsonI.php';
require_once '../vendor/webfiori/jsonx/src/JsonTypes.php';
require_once '../vendor/webfiori/jsonx/src/Json.php';
require_once '../src/ParamTypes.php';
require_once '../src/AbstractWebService.php';
require_once '../src/APIFilter.php';
require_once '../src/WebServicesManager.php';
require_once '../src/RequestParameter.php';
require_once '../src/Request.php';
require_once '../src/Response.php';
require_once '../src/Uri.php';
require_once 'GetRandomService.php';
