<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Exceptions\NotFoundException;
use WebFiori\Http\Exceptions\BadRequestException;
use WebFiori\Http\Exceptions\UnauthorizedException;

class ExceptionTestService extends WebService {
    public function __construct() {
        parent::__construct('exception-test');
        $this->addRequestMethod('GET');
        $this->addRequestMethod('POST');
    }
    
    public function isAuthorized(): bool {
        return true;
    }
    
    public function processRequest() {
    }
    
    #[ResponseBody]
    public function getUser() {
        $testId = $_GET['test_id'] ?? 0;
        if ($testId == 404) {
            throw new NotFoundException('User not found');
        }
        if ($testId == 400) {
            throw new BadRequestException('Invalid user ID');
        }
        return ['id' => $testId];
    }
    
    public function createUser() {
        throw new UnauthorizedException('Authentication required');
    }
    
    public function getError() {
        throw new \Exception('Generic error');
    }
}
