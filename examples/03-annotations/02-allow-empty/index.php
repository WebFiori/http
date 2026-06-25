<?php

require_once '../../../vendor/autoload.php';
require_once 'NotesService.php';

use WebFiori\Http\RequestProcessor;

$processor = new RequestProcessor();
$processor->process(new NotesService());
