<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;

class NonAnnotatedService extends WebService {
    public function __construct() {
        parent::__construct('non-annotated');
        $this->setDescription('A traditional service');
    }
}
