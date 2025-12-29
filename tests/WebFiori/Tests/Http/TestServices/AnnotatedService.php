<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

#[RestController('annotated-service', 'A service configured via annotations')]
class AnnotatedService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', ParamType::STRING, true)]
    public function sayHi() {
        $name = $this->getParamVal('name');
        
        if ($name !== null) {
            return "Hi ".$name.'!';
        }
        return "Hi user!";
    }
    #[DeleteMapping]
    #[ResponseBody]
    #[RequestParam('id', ParamType::INT)]
    #[PreAuthorize("isAuthenticated() && hasRole('ADMIN') && hasAuthority('USER_DELETE')")]
    public function delete() {
        $id = $this->getParamVal('id');
        return "Delete user with ID: ".$id;
    }
}
