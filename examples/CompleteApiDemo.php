<?php
require_once '../vendor/autoload.php';

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Exceptions\NotFoundException;
use WebFiori\Http\Exceptions\BadRequestException;
use WebFiori\Http\WebService;

#[RestController('api-demo', 'User management API')]
class CompleteApiDemo extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', 'int', true, null, 'User ID to retrieve specific user')]
    public function getUsers(): array {
        $id = $this->getParamVal('id');
        
        if ($id) {
            // Get specific user
            if ($id <= 0) {
                throw new BadRequestException('Invalid user ID');
            }
            
            if ($id === 404) {
                throw new NotFoundException('User not found');
            }
            
            return [
                'user' => [
                    'id' => $id,
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ]
            ];
        } else {
            // Get all users
            return [
                'users' => [
                    ['id' => 1, 'name' => 'John Doe'],
                    ['id' => 2, 'name' => 'Jane Smith']
                ],
                'total' => 2
            ];
        }
    }
    
    #[PostMapping]
    #[ResponseBody(status: 201)]
    #[RequestParam('name', 'string')]
    #[RequestParam('email', 'email')]
    #[PreAuthorize("hasAuthority('USER_CREATE')")]
    public function createUser(): array {
        $name = $this->getParamVal('name');
        $email = $this->getParamVal('email');
        
        if (empty($name)) {
            throw new BadRequestException('Name is required');
        }
        
        return [
            'message' => 'User created successfully',
            'user' => [
                'id' => rand(1000, 9999),
                'name' => $name,
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    #[PutMapping]
    #[ResponseBody]
    #[RequestParam('id', 'int')]
    #[RequestParam('name', 'string', true)]
    #[RequestParam('email', 'email', true)]
    #[PreAuthorize("hasAuthority('USER_UPDATE')")]
    public function updateUser(): array {
        $id = $this->getParamVal('id');
        $name = $this->getParamVal('name');
        $email = $this->getParamVal('email');
        
        if ($id === 404) {
            throw new NotFoundException('User not found');
        }
        
        $updates = array_filter([
            'name' => $name,
            'email' => $email
        ]);
        
        return [
            'message' => 'User updated successfully',
            'user' => [
                'id' => $id,
                'updates' => $updates,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    #[DeleteMapping]
    #[ResponseBody(status: 204)]
    #[RequestParam('id', 'int')]
    #[PreAuthorize("hasRole('ADMIN')")]
    public function deleteUser(): null {
        $id = $this->getParamVal('id');
        
        if ($id === 404) {
            throw new NotFoundException('User not found');
        }
        
        // Simulate deletion
        return null; // Auto-converts to 204 No Content
    }
}
