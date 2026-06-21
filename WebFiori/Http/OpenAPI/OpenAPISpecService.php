<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2026-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http\OpenAPI;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Json\JsonI;

/**
 * Built-in service that exposes an OpenAPI specification via GET.
 * 
 * The service scans a configured namespace for #[RestController] classes
 * and returns the generated OpenAPI JSON spec.
 * 
 * Usage:
 * ```php
 * $spec = new OpenAPISpecService('App\\Apis', '/apis', 'My API', '1.0.0');
 * $processor = new RequestProcessor();
 * $processor->process($spec);
 * ```
 *
 * @author Ibrahim
 */
#[RestController(name: 'openapi', description: 'OpenAPI specification endpoint')]
class OpenAPISpecService extends WebService {
    private string $namespace;
    private string $apiBasePath;
    private string $apiTitle;
    private string $apiVersion;

    /**
     * Creates a new OpenAPI spec service.
     * 
     * @param string $namespace The namespace to scan for #[RestController] services.
     * @param string $basePath The base path prefix for API routes in the spec.
     * @param string $title The API title for the info object.
     * @param string $version The API version string.
     */
    public function __construct(string $namespace, string $basePath = '', string $title = '', string $version = '1.0.0') {
        parent::__construct('openapi');
        $this->namespace = $namespace;
        $this->apiBasePath = $basePath;
        $this->apiTitle = $title;
        $this->apiVersion = $version;
    }

    public function isAuthorized(): bool {
        return true;
    }

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getSpec(): JsonI {
        $generator = new OpenAPIGenerator();

        return $generator->generateFromNamespace(
            $this->namespace,
            $this->apiTitle,
            $this->apiVersion,
            $this->apiBasePath
        );
    }

    public function processRequest() {
    }
}
