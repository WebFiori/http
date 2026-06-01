<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http\OpenAPI;

use WebFiori\Http\WebService;

/**
 * Standalone generator for OpenAPI 3.x specifications from WebService instances.
 * 
 * This class decouples OpenAPI generation from WebServicesManager, allowing
 * spec generation without a full manager setup.
 *
 * @author Ibrahim
 */
class OpenAPIGenerator {
    /**
     * Generates an OpenAPI specification from an array of web services.
     * 
     * @param WebService[] $services Array of web service instances.
     * @param string $description API description for the info object.
     * @param string $version API version string.
     * @param string $basePath Base path prefix for all service routes.
     * 
     * @return OpenAPIObj The generated OpenAPI specification object.
     */
    public function generate(array $services, string $description = '', string $version = '1.0.0', string $basePath = '') : OpenAPIObj {
        $info = new InfoObj($description, $version);
        $openapi = new OpenAPIObj($info);
        $paths = new PathsObj();

        foreach ($services as $service) {
            if ($service instanceof WebService) {
                $path = $basePath.'/'.$service->getName();
                $paths->addPath($path, $service->toPathItemObj());
            }
        }

        $openapi->setPaths($paths);

        return $openapi;
    }
}
