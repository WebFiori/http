<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\Consumes;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\MediaType;
use WebFiori\Http\ResponseEntity;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

/**
 * A file upload service demonstrating the #[Consumes] annotation.
 * 
 * Shows how to:
 * - Accept binary uploads with application/octet-stream
 * - Accept multiple content types on a single method
 * - Mix standard (form-encoded) and non-standard content types
 */
#[RestController('files', 'File management service')]
#[AllowAnonymous]
class FileUploadService extends WebService {
    /**
     * Upload a raw binary file.
     * 
     * Only accepts application/octet-stream. Parameter filtering is skipped
     * automatically — the raw body is available via php://input.
     */
    #[PostMapping]
    #[Consumes(MediaType::OCTET_STREAM)]
    #[ResponseBody]
    public function uploadBinary(): ResponseEntity {
        $body = file_get_contents('php://input');
        $size = strlen($body);

        if ($size === 0) {
            return ResponseEntity::badRequest(new Json([
                'message' => 'Empty body',
            ]));
        }

        return ResponseEntity::created(new Json([
            'message' => 'File uploaded',
            'size' => $size,
            'md5' => md5($body),
        ]));
    }

    /**
     * Upload XML data.
     * 
     * Accepts both application/xml and text/xml. Demonstrates multiple
     * content types on a single method.
     */
    #[PutMapping]
    #[Consumes(MediaType::XML, 'text/xml')]
    #[ResponseBody]
    public function uploadXml(): ResponseEntity {
        $body = file_get_contents('php://input');

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if ($xml === false) {
            return ResponseEntity::badRequest(new Json([
                'message' => 'Invalid XML',
            ]));
        }

        return ResponseEntity::ok(new Json([
            'message' => 'XML received',
            'root_element' => $xml->getName(),
        ]));
    }
}
