<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\RequestParameter;
use WebFiori\Json\Json;
/**
 * Description of SumNumbersService
 *
 * @author Ibrahim
 */
class SumNumbersService extends AbstractNumbersService {
    
    public function __construct() {
        parent::__construct('sum-array');
        $this->addRequestMethod('post');
        $this->addRequestMethod('get');
        $this->setDescription('Returns a JSON string that has the sum of array of numbers.');
        $this->addParameter(new RequestParameter('numbers', 'array'));
    }

    public function processRequest() {
        $sum = 0;
        $numbersArr = $this->getParamVal('numbers');
        foreach ($numbersArr as $num) {
            if (gettype($num) == 'integer' || gettype($num) == 'double') {
                $sum += $num;
            }
        }
        $j = new Json();
        $j->add('sum', $sum);
        $this->send('application/json', $j);
    }

}
