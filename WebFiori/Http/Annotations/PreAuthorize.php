<?php
namespace WebFiori\Http\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class PreAuthorize {
    public function __construct(
        public readonly string $expression
    ) {}
}
