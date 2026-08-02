<?php

require_once '../../../vendor/autoload.php';
require_once 'FileUploadService.php';

use WebFiori\Http\RequestProcessor;
use WebFiori\Http\Request;

$processor = new RequestProcessor();
$processor->process(new FileUploadService(), Request::createFromGlobals());
