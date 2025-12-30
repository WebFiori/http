<?php
namespace WebFiori\Http;

/**
 * Interface for representing an authenticated security principal in the security context.
 */
interface SecurityPrincipal {
    /**
     * Get the user's unique identifier.
     * 
     * @return int|string User ID
     */
    public function getId(): int|string;
    
    /**
     * Get the user's name.
     * 
     * @return string User name
     */
    public function getName(): string;
    
    /**
     * Get the user's roles.
     * 
     * @return array Array of role names
     *               Example: ['USER', 'ADMIN', 'MODERATOR']
     */
    public function getRoles(): array;
    
    /**
     * Get the user's authorities/permissions.
     * 
     * @return array Array of authority names
     *               Example: ['USER_CREATE', 'USER_UPDATE', 'USER_DELETE']
     */
    public function getAuthorities(): array;
    
    /**
     * Check if the user account is active.
     * 
     * @return bool True if user is active
     */
    public function isActive(): bool;
}
