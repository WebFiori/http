<?php
namespace WebFiori\Http\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RestController {
    public function __construct(
        public readonly string $name = '',
        public readonly string $description = ''
    ) {}
}
