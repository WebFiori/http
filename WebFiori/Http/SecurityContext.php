<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2025-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 * 
 */
namespace WebFiori\Http;

/**
 * Security context for managing authentication and authorization state.
 * 
 * Provides static methods to manage the current user's authentication status,
 * roles, and authorities for request-level security checks.
 * 
 * @author Ibrahim
 */
class SecurityContext {
    /** @var array User authorities/permissions (e.g., ['USER_CREATE', 'USER_DELETE']) - deprecated, use user object */
    private static array $authorities = [];
    /** @var SecurityPrincipal|null Current authenticated user */
    private static ?SecurityPrincipal $currentUser = null;

    /** @var array User roles (e.g., ['USER', 'ADMIN']) - deprecated, use user object */
    private static array $roles = [];

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
     *                           
     *                           Simple expressions:
     *                           - "hasRole('ADMIN')" - Check single role
     *                           - "hasAuthority('USER_CREATE')" - Check single authority
     *                           - "isAuthenticated()" - Check if user is logged in
     *                           - "permitAll()" - Always allow access
     *                           
     *                           Multiple values:
     *                           - "hasAnyRole('ADMIN', 'MODERATOR')" - Check any of multiple roles
     *                           - "hasAnyAuthority('USER_CREATE', 'USER_UPDATE')" - Check any of multiple authorities
     *                           
     *                           Complex boolean expressions:
     *                           - "hasRole('ADMIN') && hasAuthority('USER_CREATE')" - Both conditions must be true
     *                           - "hasRole('ADMIN') || hasRole('MODERATOR')" - Either condition can be true
     *                           - "isAuthenticated() && hasAnyRole('USER', 'ADMIN')" - Authenticated with any role
     * 
     * @return bool True if expression evaluates to true
     * @throws \InvalidArgumentException If expression is invalid
     */
    public static function evaluateExpression(string $expression): bool {
        $expression = trim($expression);

        if (empty($expression)) {
            throw new \InvalidArgumentException('Security expression cannot be empty');
        }

        // Handle complex boolean expressions with && and ||
        if (strpos($expression, '&&') !== false) {
            return self::evaluateAndExpression($expression);
        }

        if (strpos($expression, '||') !== false) {
            return self::evaluateOrExpression($expression);
        }

        // Handle single expressions
        return self::evaluateSingleExpression($expression);
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
     * Get the current authenticated user.
     * 
     * @return SecurityPrincipal|null User object or null if not authenticated
     */
    public static function getCurrentUser(): ?SecurityPrincipal {
        return self::$currentUser;
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
     * Check if a user is currently authenticated.
     * 
     * @return bool True if user is authenticated and active
     */
    public static function isAuthenticated(): bool {
        return self::$currentUser !== null && self::$currentUser->isActive();
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
     * Set the current authenticated user.
     * 
     * @param SecurityPrincipal|null $user User object or null for unauthenticated
     */
    public static function setCurrentUser(?SecurityPrincipal $user): void {
        self::$currentUser = $user;

        // Update legacy arrays for backward compatibility
        if ($user) {
            self::$roles = $user->getRoles();
            self::$authorities = $user->getAuthorities();
        } else {
            self::$roles = [];
            self::$authorities = [];
        }
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
     * Evaluate AND expression (all conditions must be true).
     */
    private static function evaluateAndExpression(string $expression): bool {
        $parts = array_map('trim', explode('&&', $expression));

        foreach ($parts as $part) {
            if (!self::evaluateSingleExpression($part)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate OR expression (at least one condition must be true).
     */
    private static function evaluateOrExpression(string $expression): bool {
        $parts = array_map('trim', explode('||', $expression));

        foreach ($parts as $part) {
            if (self::evaluateSingleExpression($part)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate single security expression.
     */
    private static function evaluateSingleExpression(string $expression): bool {
        // Handle hasRole('ROLE_NAME')
        if (preg_match("/hasRole\('([^']+)'\)/", $expression, $matches)) {
            return self::hasRole($matches[1]);
        }

        // Handle hasAnyRole('ROLE1', 'ROLE2', ...)
        if (preg_match("/hasAnyRole\(([^)]+)\)/", $expression, $matches)) {
            $roles = self::parseArgumentList($matches[1]);

            foreach ($roles as $role) {
                if (self::hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        // Handle hasAuthority('AUTHORITY_NAME')
        if (preg_match("/hasAuthority\('([^']+)'\)/", $expression, $matches)) {
            return self::hasAuthority($matches[1]);
        }

        // Handle hasAnyAuthority('AUTH1', 'AUTH2', ...)
        if (preg_match("/hasAnyAuthority\(([^)]+)\)/", $expression, $matches)) {
            $authorities = self::parseArgumentList($matches[1]);

            foreach ($authorities as $authority) {
                if (self::hasAuthority($authority)) {
                    return true;
                }
            }

            return false;
        }

        // Handle isAuthenticated()
        if ($expression === 'isAuthenticated()') {
            return self::isAuthenticated();
        }

        // Handle permitAll()
        if ($expression === 'permitAll()') {
            return true;
        }

        throw new \InvalidArgumentException("Invalid security expression: '$expression'");
    }

    /**
     * Parse comma-separated argument list from function call.
     */
    private static function parseArgumentList(string $args): array {
        $result = [];
        $parts = explode(',', $args);

        foreach ($parts as $part) {
            $part = trim($part);

            if (preg_match("/^'([^']+)'$/", $part, $matches)) {
                $result[] = $matches[1];
            } else {
                throw new \InvalidArgumentException("Invalid argument format: '$part'");
            }
        }

        return $result;
    }
}
