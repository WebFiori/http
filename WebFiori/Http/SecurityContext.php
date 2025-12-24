<?php
namespace WebFiori\Http;

/**
 * Security context for managing authentication and authorization state.
 * 
 * Provides static methods to manage the current user's authentication status,
 * roles, and authorities for request-level security checks.
 */
class SecurityContext {
    /** @var array|null Current authenticated user data */
    private static ?array $currentUser = null;
    
    /** @var array User roles (e.g., ['USER', 'ADMIN']) */
    private static array $roles = [];
    
    /** @var array User authorities/permissions (e.g., ['USER_CREATE', 'USER_DELETE']) */
    private static array $authorities = [];
    
    /**
     * Set the current authenticated user.
     * 
     * @param array|null $user User data array or null for unauthenticated
     *                         Example: ['id' => 123, 'name' => 'John Doe', 'email' => 'john@example.com']
     */
    public static function setCurrentUser(?array $user): void {
        self::$currentUser = $user;
    }
    
    /**
     * Get the current authenticated user.
     * 
     * @return array|null User data or null if not authenticated
     */
    public static function getCurrentUser(): ?array {
        return self::$currentUser;
    }
    
    /**
     * Set user roles.
     * 
     * @param array $roles Array of role names
     *                     Example: ['USER', 'ADMIN', 'MODERATOR']
     */
    public static function setRoles(array $roles): void {
        self::$roles = $roles;
    }
    
    /**
     * Get user roles.
     * 
     * @return array Array of role names
     */
    public static function getRoles(): array {
        return self::$roles;
    }
    
    /**
     * Set user authorities/permissions.
     * 
     * @param array $authorities Array of authority names
     *                           Example: ['USER_CREATE', 'USER_UPDATE', 'USER_DELETE', 'REPORT_VIEW']
     */
    public static function setAuthorities(array $authorities): void {
        self::$authorities = $authorities;
    }
    
    /**
     * Get user authorities/permissions.
     * 
     * @return array Array of authority names
     */
    public static function getAuthorities(): array {
        return self::$authorities;
    }
    
    /**
     * Check if user has a specific role.
     * 
     * @param string $role Role name to check
     *                     Example: 'ADMIN', 'USER', 'MODERATOR'
     * @return bool True if user has the role
     */
    public static function hasRole(string $role): bool {
        return in_array($role, self::$roles);
    }
    
    /**
     * Check if user has a specific authority/permission.
     * 
     * @param string $authority Authority name to check
     *                          Example: 'USER_CREATE', 'USER_DELETE', 'REPORT_VIEW'
     * @return bool True if user has the authority
     */
    public static function hasAuthority(string $authority): bool {
        return in_array($authority, self::$authorities);
    }
    
    /**
     * Check if a user is currently authenticated.
     * 
     * @return bool True if user is authenticated
     */
    public static function isAuthenticated(): bool {
        return self::$currentUser !== null;
    }
    
    /**
     * Clear all security context data.
     */
    public static function clear(): void {
        self::$currentUser = null;
        self::$roles = [];
        self::$authorities = [];
    }
    
    /**
     * Evaluate security expression.
     * 
     * @param string $expression Security expression to evaluate
     *                           Example: "hasRole('ADMIN')", "hasAuthority('USER_CREATE')", "isAuthenticated()"
     * @return bool True if expression evaluates to true
     */
    public static function evaluateExpression(string $expression): bool {
        $evalResult = false;
        // Handle hasRole('ROLE_NAME')
        if (preg_match("/hasRole\('([^']+)'\)/", $expression, $matches)) {
            $evalResult = self::hasRole($matches[1]);
        }
        
        // Handle hasAuthority('AUTHORITY_NAME')
        if (preg_match("/hasAuthority\('([^']+)'\)/", $expression, $matches)) {
            $evalResult &= self::hasAuthority($matches[1]);
        }
        
        // Handle isAuthenticated()
        if ($expression === 'isAuthenticated()') {
            $evalResult &= self::isAuthenticated();
        }
        
        return $evalResult;
    }
}
