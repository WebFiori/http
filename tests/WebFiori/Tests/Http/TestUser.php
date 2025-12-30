<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\SecurityPrincipal;

/**
 * Test implementation of SecurityPrincipal for testing purposes.
 */
class TestUser implements SecurityPrincipal {
    private int|string $id;
    private string $name;
    private array $roles;
    private array $authorities;
    private bool $active;
    
    public function __construct(int|string $id, array $roles = [], array $authorities = [], bool $active = false, string $name = 'Test User') {
        $this->id = $id;
        $this->name = $name;
        $this->roles = $roles;
        $this->authorities = $authorities;
        $this->active = $active;
    }
    
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
