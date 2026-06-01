<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * Processes a single WebService against an HTTP request without requiring
 * manual service registration or a full WebServicesManager setup.
 * 
 * This class provides a simplified entry point for processing a web service.
 * It handles content type validation, HTTP method checking, parameter filtering,
 * authorization, method invocation, and response serialization.
 * 
 * Usage:
 * ```php
 * $processor = new RequestProcessor();
 * $processor->process(new MyService(), Request::createFromGlobals());
 * ```
 *
 * @author Ibrahim
 */
class RequestProcessor {
    /**
     * Process a request against a specific web service.
     * 
     * The processor runs the full pipeline:
     * 1. Content type validation
     * 2. HTTP method matching
     * 3. Parameter filtering and validation
     * 4. Authorization check
     * 5. Method invocation
     * 6. Response serialization
     * 
     * @param WebService $service The service to process.
     * @param Request|null $request The incoming HTTP request. If null, creates from globals.
     * @param resource|null $outputStream Optional output stream for testing.
     */
    public function process(WebService $service, ?Request $request = null, $outputStream = null) : void {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        $manager = new WebServicesManager($request);
        $manager->addService($service);

        if ($outputStream !== null) {
            $manager->setOutputStream($outputStream);
        }

        $manager->process();
    }
}
