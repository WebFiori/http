<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebServicesManager;
/**
 * Description of SampleAPI
 *
 * @author Ibrahim
 */
class SampleServicesManager extends WebServicesManager {
    public function __construct() {
        parent::__construct();
        
        $this->addService(new AddNubmersService());

        $this->setVersion('1.0.1');
        
        $this->addService(new SumNumbersService());
        $this->addService(new GetUserProfileService());
        $this->addService(new CreateUserProfileService());
        $this->addService(new MulNubmersService());
    }
    
}
