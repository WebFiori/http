<?php

use WebFiori\Http\RequestProcessor;

require_once '../../../vendor/autoload.php';
require_once 'BasicAuthService.php';

$processor = new RequestProcessor();
$processor->process(new BasicAuthService());
