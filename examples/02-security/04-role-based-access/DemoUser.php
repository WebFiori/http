<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\SecurityPrincipal;

/**
 * Demo user implementation
 */
class DemoUser implements SecurityPrincipal {
    
    public function __construct(
        private int|string $id,
        private string $name,
        private array $roles = [],
        private array $authorities = [],
        private bool $active = true
    ) {}
    
    public function getId(): int|string {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getRoles(): array {
        return $this->roles;
    }
    
    public function getAuthorities(): array {
        return $this->authorities;
    }
    
    public function isActive(): bool {
        return $this->active;
    }
}
