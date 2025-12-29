<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\UserInterface;

/**
 * Test implementation of UserInterface for testing purposes.
 */
class TestUser implements UserInterface {
    private int|string $id;
    private array $roles;
    private array $authorities;
    private bool $active;
    
    public function __construct(int|string $id, array $roles = [], array $authorities = [], bool $active = false) {
        $this->id = $id;
        $this->roles = $roles;
        $this->authorities = $authorities;
        $this->active = $active;
    }
    
    public function getId(): int|string {
        return $this->id;
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
