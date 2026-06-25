<?php

use WebFiori\Http\RequestProcessor;

require_once '../../../vendor/autoload.php';
require_once 'HelloService.php';

$processor = new RequestProcessor();
$processor->process(new HelloService());
