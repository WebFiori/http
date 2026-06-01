<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2026-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * Constants for common HTTP media types (MIME types).
 * 
 * Use with #[Produces] attribute for content negotiation.
 *
 * @author Ibrahim
 */
class MediaType {
    const CSV = 'text/csv';
    const FORM = 'application/x-www-form-urlencoded';
    const HTML = 'text/html';
    const JSON = 'application/json';
    const MULTIPART = 'multipart/form-data';
    const OCTET_STREAM = 'application/octet-stream';
    const PDF = 'application/pdf';
    const PLAIN = 'text/plain';
    const XML = 'application/xml';
}
