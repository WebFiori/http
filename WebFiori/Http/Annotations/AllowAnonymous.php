<?php
namespace WebFiori\Http\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class AllowAnonymous {
}
