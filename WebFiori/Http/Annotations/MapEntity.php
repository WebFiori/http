<?php

namespace WebFiori\Http\Annotations;

use Attribute;

/**
 * Attribute for mapping HTTP request parameters to entity objects
 */
#[Attribute(Attribute::TARGET_METHOD)]
class MapEntity {
    
    public function __construct(
        public string $entityClass,
        public array $setters = []
    ) {}
}
