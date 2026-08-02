<?php

require_once '../../../vendor/autoload.php';
require_once 'FileUploadService.php';

use WebFiori\Http\Request;
use WebFiori\Http\RequestProcessor;

$processor = new RequestProcessor();
$processor->process(new FileUploadService(), Request::createFromGlobals());
