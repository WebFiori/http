<?php

require_once '../../../vendor/autoload.php';
require_once __DIR__ . '/ProductService.php';

use WebFiori\Http\OpenAPI\OpenAPIGenerator;
use WebFiori\Http\OpenAPI\OpenAPISpecService;
use WebFiori\Http\RequestProcessor;

// --- Approach 1: Namespace scanning with OpenAPIGenerator ---
// (Classes must be autoloadable or already loaded)

$generator = new OpenAPIGenerator();
$spec = $generator->generate(
    OpenAPIGenerator::discoverServices(''),  // Scans global namespace here
    'Shop API',
    '1.0.0',
    '/apis'
);

header('Content-Type: application/json');
echo $spec->toJSON();

// --- Approach 2: Built-in OpenAPISpecService ---
// Uncomment below to serve the spec as a live endpoint:
//
// $specService = new OpenAPISpecService(
//     'App\\Apis',       // Namespace to scan
//     '/apis',           // Base path in spec
//     'Shop API',        // Title
//     '1.0.0'            // Version
// );
// $processor = new RequestProcessor();
// $processor->process($specService);
