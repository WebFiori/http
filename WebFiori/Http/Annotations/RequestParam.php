<?php
namespace WebFiori\Http\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RequestParam {
    public function __construct(
        public readonly string $name,
        public readonly string $type = 'string',
        public readonly bool $optional = false,
        public readonly mixed $default = null,
        public readonly string $description = ''
    ) {}
}
