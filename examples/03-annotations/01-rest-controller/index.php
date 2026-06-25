<?php

require_once '../../../vendor/autoload.php';
require_once 'TaskService.php';

use WebFiori\Http\RequestProcessor;

$processor = new RequestProcessor();
$processor->process(new TaskService());
