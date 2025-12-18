<?php
namespace WebFiori\Http;

class SecurityContext {
    private static ?array $currentUser = null;
    private static array $roles = [];
    private static array $authorities = [];
    
    public static function setCurrentUser(?array $user): void {
        self::$currentUser = $user;
    }
    
    public static function getCurrentUser(): ?array {
        return self::$currentUser;
    }
    
    public static function setRoles(array $roles): void {
        self::$roles = $roles;
    }
    
    public static function getRoles(): array {
        return self::$roles;
    }
    
    public static function setAuthorities(array $authorities): void {
        self::$authorities = $authorities;
    }
    
    public static function getAuthorities(): array {
        return self::$authorities;
    }
    
    public static function hasRole(string $role): bool {
        return in_array($role, self::$roles);
    }
    
    public static function hasAuthority(string $authority): bool {
        return in_array($authority, self::$authorities);
    }
    
    public static function isAuthenticated(): bool {
        return self::$currentUser !== null;
    }
    
    public static function clear(): void {
        self::$currentUser = null;
        self::$roles = [];
        self::$authorities = [];
    }
}
