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

use WebFiori\Http\Annotations\RestController;
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
                $path = $basePath.'/'.$service->getPath();
                $paths->addPath($path, $service->toPathItemObj());
            }
        }

        $openapi->setPaths($paths);

        return $openapi;
    }

    /**
     * Generates an OpenAPI specification by scanning a namespace for #[RestController] classes.
     * 
     * Classes must already be autoloadable (e.g., via Composer). The method finds all
     * declared classes in the given namespace that extend WebService and have the
     * #[RestController] attribute.
     * 
     * @param string $namespace The namespace to scan (e.g., 'App\\Apis').
     * @param string $description API title/description for the info object.
     * @param string $version API version string.
     * @param string $basePath Base path prefix for all service routes.
     * 
     * @return OpenAPIObj The generated OpenAPI specification object.
     */
    public function generateFromNamespace(string $namespace, string $description = '', string $version = '1.0.0', string $basePath = '') : OpenAPIObj {
        $services = self::discoverServices($namespace);

        return $this->generate($services, $description, $version, $basePath);
    }

    /**
     * Discovers WebService instances in a given namespace.
     * 
     * Scans all declared classes for those belonging to the namespace,
     * extending WebService, having #[RestController], and not being abstract.
     * 
     * @param string $namespace The namespace to scan.
     * 
     * @return WebService[] Array of instantiated service objects.
     */
    public static function discoverServices(string $namespace) : array {
        $namespace = rtrim($namespace, '\\') . '\\';
        $services = [];

        foreach (get_declared_classes() as $class) {
            if (!str_starts_with($class, $namespace)) {
                continue;
            }

            if (!is_subclass_of($class, WebService::class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);

            if ($reflection->isAbstract()) {
                continue;
            }

            if (empty($reflection->getAttributes(RestController::class))) {
                continue;
            }

            $services[] = new $class();
        }

        return $services;
    }
}
