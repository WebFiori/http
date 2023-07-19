<?php
namespace webfiori\tests\http;

use PHPUnit\Framework\TestCase;
use webfiori\http\APIFilter;
use webfiori\http\RequestParameter;
use webfiori\json\Json;
/**
 * Description of APIFilterTest
 *
 * @author Eng.Ibrahim
 */
class APIFilterTest extends TestCase {
    /**
     *
     * @var APIFilter 
     */
    private $apiFilter;
    /**
     * @test
     */
    public function testFilterGet00() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(0,count($filtered));
    }
    /**
     * @test
     */
    public function testFilterGet01() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['username'] = 'Admin\'';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Admin\'',$filtered['username']);
        $this->assertTrue(isset($nonFiltered['username']));
        $this->assertEquals('Admin\'',$nonFiltered['username']);
    }
    /**
     * @test
     */
    public function testFilterGet02() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['username'] = '<script>alert(Admin)</script>';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('alert(Admin)',$filtered['username']);
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['username']));
        $this->assertEquals('<script>alert(Admin)</script>',$nonFiltered['username']);
    }
    /**
     * @test
     */
    public function testFilterGet03() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['username'] = '<?php echo "<script>alert("Oh No!")</script>";?>Book';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Book',$filtered['username']);
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['username']));
        $this->assertEquals('<?php echo "<script>alert("Oh No!")</script>";?>Book',$nonFiltered['username']);
    }
    /**
     * @test
     */
    public function testFilterGet04() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('password', 'integer');
        $param01->setMinValue(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['username'] = 'Admin';
        $_GET['password'] = '100';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Admin',$filtered['username']);
        $this->assertTrue(isset($filtered['password']));
        $this->assertEquals(APIFilter::INVALID,$filtered['password']);
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['username']));
        $this->assertEquals('Admin',$nonFiltered['username']);
        $this->assertTrue(isset($nonFiltered['password']));
        $this->assertEquals('100',$nonFiltered['password']);
    }
    /**
     * @test
     */
    public function testFilterGet05() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('password', 'integer');
        $param01->setMinValue(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['username'] = 'Admin';
        $_GET['password'] = '1002000';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Admin',$filtered['username']);
        $this->assertTrue(isset($filtered['password']));
        $this->assertEquals('1002000',$filtered['password']);
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['username']));
        $this->assertEquals('Admin',$nonFiltered['username']);
        $this->assertTrue(isset($nonFiltered['password']));
        $this->assertEquals('1002000',$nonFiltered['password']);
    }
    /**
     * @test
     */
    public function testFilterGet06() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('password', 'integer');
        $param01->setMinValue(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['username'] = 'Admin';
        $_GET['password'] = '1002000with some text<script></script>';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Admin',$filtered['username']);
        $this->assertTrue(isset($filtered['password']));
        $this->assertEquals(APIFilter::INVALID,$filtered['password']);
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['username']));
        $this->assertEquals('Admin',$nonFiltered['username']);
        $this->assertTrue(isset($nonFiltered['password']));
        $this->assertEquals('1002000with some text<script></script>',$nonFiltered['password']);
    }
    /**
     * @test
     */
    public function testFilterGet07() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','float');
        $this->assertEquals('double', $param00->getType());
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-number', 'float');
        $this->assertEquals('double', $param01->getType());
        $param01->setMinValue(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = 'Admin';
        $_GET['second-number'] = 'yc with some text<script></script>';
        $this->apiFilter->filterGET();
        
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['first-number']));
        $this->assertEquals(APIFilter::INVALID,$filtered['first-number']);
        $this->assertTrue(isset($filtered['second-number']));
        $this->assertEquals(APIFilter::INVALID,$filtered['second-number']);
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['first-number']));
        $this->assertEquals('Admin',$nonFiltered['first-number']);
        $this->assertTrue(isset($nonFiltered['second-number']));
        $this->assertEquals('yc with some text<script></script>',$nonFiltered['second-number']);
    }
    /**
     * @test
     */
    public function testFilterGet08() {
        foreach ($_GET as $key => $value) {
            unset($_GET[$key]);
        }
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','float');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-number', 'float');
        $param01->setDefault(1000);
        $this->assertTrue($param01->setMinValue(1000000));
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = 'Admin';
        $_GET['second-number'] = "0.15";
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        
        
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['first-number']));
        $this->assertEquals(APIFilter::INVALID,$filtered['first-number']);
        $this->assertTrue(isset($filtered['second-number']));
        $this->assertSame(1000, $filtered['second-number']);
        
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['first-number']));
        $this->assertEquals('Admin',$nonFiltered['first-number']);
        
    }
    /**
     * @test
     */
    public function testFilterGet09() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','float');
        $this->apiFilter->addRequestParameter($param00);
        
        $param01 = new RequestParameter('second-number', 'float');
        $param01->setDefault(1000);
        $this->assertEquals(1000, $param01->getDefault());
        $param01->setMinValue(1000000);
        
        $this->apiFilter->addRequestParameter($param01);
        $_GET = [];
        $_GET['first-number'] = 'Admin';
        $_GET['second-number'] = '';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['first-number']));
        $this->assertEquals(APIFilter::INVALID,$filtered['first-number']);
        $this->assertTrue(isset($filtered['second-number']));
        $this->assertEquals(1000,$filtered['second-number']);

        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertTrue(isset($nonFiltered['first-number']));
        $this->assertEquals('Admin',$nonFiltered['first-number']);
        $this->assertTrue(isset($nonFiltered['second-number']));
        $this->assertEquals('',$nonFiltered['second-number']);
    }
    /**
     * @test
     */
    public function testFilterGet10() {
        foreach ($_GET as $key => $value) {
            unset($_GET[$key]);
        }
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','float',true);
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-number', 'float');
        $param01->setDefault(1000);
        $param01->setMinValue(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['second-number'] = '100076800';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue($filtered['first-number'] === null);
        $this->assertTrue(isset($filtered['second-number']));
        $this->assertSame(100076800.0,$filtered['second-number']);

        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertNull($nonFiltered['first-number']);
        $this->assertTrue(isset($nonFiltered['second-number']));
        $this->assertEquals('100076800',$nonFiltered['second-number']);
    }
    /**
     * @test
     */
    public function testFilterGet11() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('my-string');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['my-string'] = '';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID,$filtered['my-string']);
    }
    /**
     * @test
     */
    public function testFilterGet12() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('my-string');
        $param00->setIsEmptyStringAllowed(true);
        $this->apiFilter->addRequestParameter($param00);
        $_GET['my-string'] = '';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals('',$filtered['my-string']);
    }
    /**
     * @test
     */
    public function testFilterGet13() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('redirect', 'url');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['redirect'] = '';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID,$filtered['redirect']);
    }
    /**
     * @test
     */
    public function testFilterGet14() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('redirect', 'url');
        $this->assertEquals('url', $param00->getType());
        $this->apiFilter->addRequestParameter($param00);
        $_GET['redirect'] = 'programmingacademia.com';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID,$filtered['redirect']);
    }
    /**
     * @test
     */
    public function testFilterGet15() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('redirect', 'url');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('redirect-secure', 'url');
        $this->apiFilter->addRequestParameter($param01);
        $_GET['redirect'] = 'http://programmingacademia.com<script>';
        $_GET['redirect-secure'] = ' https://programmingacademia.com';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertEquals('http://programmingacademia.com',$filtered['redirect']);
        $this->assertEquals('https://programmingacademia.com',$filtered['redirect-secure']);
    }
    /**
     * @test
     */
    public function testFilterGet16() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('send-to', 'email');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['send-to'] = '@';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID,$filtered['send-to']);
    }
    /**
     * @test
     */
    public function testFilterGet17() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('send-to', 'email');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['send-to'] = 'admin@examle';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID,$filtered['send-to']);
    }
    /**
     * @test
     */
    public function testFilterGet18() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('send-to', 'email');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['send-to'] = 'admin@example.com';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals('admin@example.com',$filtered['send-to']);
    }
    /**
     * @test
     */
    public function testFilterGet19() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('send-to', 'email');
        $param00->setCustomFilterFunction(function($val,$filtered,$reqParam)
        {

            return 'Email to: '.$val;
        });
        $this->apiFilter->addRequestParameter($param00);
        $_GET['send-to'] = 'admin@example.com';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals('Email to: admin@example.com',$filtered['send-to']);
    }
    /**
     * @test
     */
    public function testFilterGet20() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('send-to', 'email');
        $param00->setCustomFilterFunction(function($val,$filtered,$reqParam)
        {

            
        });
        $this->apiFilter->addRequestParameter($param00);
        $_GET['send-to'] = 'admin@example.com';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID,$filtered['send-to']);
    }
    /**
     * @test
     */
    public function testFilterGet21() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('send-to', 'array');
        $param00->setCustomFilterFunction(function($val,$filtered,$reqParam)
        {
            return ['super@duper.com'];
        });
        $this->apiFilter->addRequestParameter($param00);
        $_GET['send-to'] = '["admin@example.com"]';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(['super@duper.com'],$filtered['send-to']);
    }
    /**
     * @test
     */
    public function testFilterGet22() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('send-to', 'array');
        $param00->setCustomFilterFunction(function($val,$filtered,$reqParam)
        {
            return $filtered;
        });
        $this->apiFilter->addRequestParameter($param00);
        $_GET['send-to'] = '["admin@example.com","hello@world.com"]';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(['admin@example.com','hello@world.com'],$filtered['send-to']);
    }
    /**
     * @test
     */
    public function testFilterGet23() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['array'] = '[false, "Hello", null, "World"]';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals([false, "Hello", null, "World"],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterGet24() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['array'] = '[false, "Hello", null, "World", 0, 1, -1, 1.5, -8.9, 0]';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals([false, "Hello", null, "World", 0, 1, -1, 1.5, -8.9, 0],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterGet25() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['array'] = '["It\'s a wonderful day. \'\' ."]';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(["It's a wonderful day. '' ."], $filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterGet26() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['array'] = "['". urlencode("01-05/2018")."' ,'". urlencode("2018/07-01")."', '". urlencode("2017/08-01")."']";
        $this->assertEquals("['01-05%2F2018' ,'2018%2F07-01', '2017%2F08-01']",$_GET['array']);
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        
        $this->assertEquals([
            '01-05/2018',
            '2018/07-01', 
            '2017/08-01'
        ],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterGet27() {
        foreach ($_GET as $key => $value) {
            unset($_GET[$key]);
        }
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','int');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-number', 'float');
  
        $param01->setMinValue(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = '44.88';
        $_GET['second-number'] = 'x100076800 inv with str';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertEquals(APIFilter::INVALID, $filtered['first-number']);
        $this->assertEquals(APIFilter::INVALID, $filtered['second-number']);

        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertEquals('44.88', $nonFiltered['first-number']);
        $this->assertEquals('x100076800 inv with str',$nonFiltered['second-number']);
    }
    /**
     * @test
     */
    public function testFilterGet28() {
        foreach ($_GET as $key => $value) {
            unset($_GET[$key]);
        }
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','int');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-number', 'float');
  
        $param01->setMinValue(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = '4488';
        $_GET['second-number'] = '100076800.776';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertSame(4488, $filtered['first-number']);
        $this->assertEquals(100076800.776, $filtered['second-number']);

        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertEquals('4488', $nonFiltered['first-number']);
        $this->assertEquals('100076800.776',$nonFiltered['second-number']);
    }
    /**
     * @test
     */
     public function testFilterGet29() {
        foreach ($_GET as $key => $value) {
            unset($_GET[$key]);
        }
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','int');
        $param00->setMaxValue(100);
        $param00->setMinValue(50);
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-number', 'float');
        $param01->setMaxValue(200); 
        $this->assertFalse($param01->setMinValue(1000000));
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = '4488';
        $_GET['second-number'] = '100076800.777';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertEquals(APIFilter::INVALID, $filtered['first-number']);
        $this->assertEquals(APIFilter::INVALID, $filtered['second-number']);

        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertEquals('4488', $nonFiltered['first-number']);
        $this->assertEquals('100076800.777',$nonFiltered['second-number']);
    }
    /**
     * @test
     */
     public function testFilterGet30() {
        foreach ($_GET as $key => $value) {
            unset($_GET[$key]);
        }
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-name');
        $param00->setMaxLength(15);
        $param00->setMinLength(5);
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('family-name');
        $this->assertTrue($param01->setMaxLength(20)); 
        $this->assertFalse($param01->setMinLength(-1));
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-name'] = 'Ibr';
        $_GET['family-name'] = 'Bin Alshikh Ali BinShikhx';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertEquals(APIFilter::INVALID, $filtered['first-name']);
        $this->assertEquals(APIFilter::INVALID, $filtered['family-name']);

        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertEquals('Ibr', $nonFiltered['first-name']);
        $this->assertEquals('Bin Alshikh Ali BinShikhx',$nonFiltered['family-name']);
    }
    /**
     * @test
     */
    public function testFilterPost01() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['array'] = '["hello"]';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(["hello"],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterPost02() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['array'] = '[1,2,6.9,8.9,99,-80]';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals([1,2,6.9,8.9,99,-80],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterPost03() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['array'] = '[true,false,null,"A , string"]';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals([true,false,null,"A , string"],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterPost04() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['array'] = "['Ooooh! it feels good!',-9986.8,true,779,false,\"A,Wrld, Is 55 imes, good. Is't\",null,-88.9,90,\"A , string\"]";
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(["Ooooh! it feels good!",-9986.8,true,779,false,"A,Wrld, Is 55 imes, good. Is't",null,-88.9,90,"A , string"],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterPost05() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['array'] = "['Ooooh! it feels good!',-9986.8,true,779,false,\"A,Wrld, <script>alert('Hello')</script> Is 55 imes, good. Is't\",null,-88.9,90,\"A , string\"]";
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(["Ooooh! it feels good!",-9986.8,true,779,false,"A,Wrld, alert('Hello') Is 55 imes, good. Is't",null,-88.9,90,"A , string"],$filtered['array']);
    }
    /**
     * @test
     */
    public function testFilterPost20() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('boolean', 'boolean');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['boolean'] = 'hello';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID,$filtered['boolean']);
        $_POST['boolean'] = '1';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue($filtered['boolean']);
        $_POST['boolean'] = '0';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertFalse($filtered['boolean']);
        $_POST['boolean'] = 'T';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue($filtered['boolean']);
        $_POST['boolean'] = 'YeS';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue($filtered['boolean']);
        $_POST['boolean'] = 'NO';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertFalse($filtered['boolean']);
    }
    /**
     * @test
     */
    public function testFilterPost06() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('bool', 'boolean');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['bool'] = "true";
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue($filtered['bool']);
    }
    /**
     * @test
     */
    public function testFilterPost07() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('bool', 'boolean');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['bool'] = "y";
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue($filtered['bool']);
    }
    /**
     * @test
     */
    public function testFilterPost08() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('bool', 'boolean');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['bool'] = "n";
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertFalse($filtered['bool']);
    }
    /**
     * @test
     */
    public function testFilterPost09() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('bool', 'boolean');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['bool'] = "some random val";
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID, $filtered['bool']);
    }
    /**
     * @test
     */
    public function testFilterPost10() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('name', 'string', true);
        $this->apiFilter->addRequestParameter($param00);
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertNull($filtered['name']);
    }
    /**
     * @test
     */
    public function testFilterPost11() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('name', 'string', true);
        $param00->setDefault('Ibrahim BinAlshikh');
        $this->apiFilter->addRequestParameter($param00);
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals('Ibrahim BinAlshikh',$filtered['name']);
    }
    /**
     * @test
     */
    public function testFilterPost12() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('bool', 'boolean');
        $param00->setCustomFilterFunction(function ($valNoFilter, $basicFilter, $requestParam) {
            if ($basicFilter === true) {
                return false;
            } else {
                return true;
            }
        });
        $this->apiFilter->addRequestParameter($param00);
        $_POST['bool'] = 't';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertFalse($filtered['bool']);
    }
    /**
     * @test
     */
    public function testFilterPost13() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('bool', 'boolean');
        $param00->setCustomFilterFunction(function ($valNoFilter, $basicFilter, $requestParam) {
            if ($basicFilter === true) {
                return false;
            } else {
                return true;
            }
        });
        $this->apiFilter->addRequestParameter($param00);
        $_POST['bool'] = 'f';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue($filtered['bool']);
    }
    /**
     * @test
     */
    public function testFilterPost14() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('bool', 'boolean');
        $param00->setCustomFilterFunction(function ($valNoFilter, $basicFilter, $requestParam) {
            if ($basicFilter == 'NOT_APLICABLE') {
                return true;
            } else {
                return true;
            }
        }, false);
        $this->apiFilter->addRequestParameter($param00);
        $_POST['bool'] = 'f';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue($filtered['bool']);
    }
    /**
     * @test
     */
    public function testFilterPost15() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('str', 'string');
        $param00->setCustomFilterFunction(function ($valNoFilter, $basicFilter, $requestParam) {
            return $basicFilter;
        });
        $param00->setIsEmptyStringAllowed(true);
        $this->apiFilter->addRequestParameter($param00);
        $_POST['str'] = '';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals('', $filtered['str']);
    }
    /**
     * @test
     */
    public function testFilterPost16() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('str', 'string');
        $param00->setCustomFilterFunction(function ($valNoFilter, $basicFilter, $requestParam) {
            return $basicFilter;
        });
        $param00->setIsEmptyStringAllowed(false);
        $this->apiFilter->addRequestParameter($param00);
        $_POST['str'] = '';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(APIFilter::INVALID, $filtered['str']);
    }
    /**
     * @test
     */
    public function testFilterPost17() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, '{"json-array":[], "another-param":"It should be ignored."}');
        $apiFilter = new APIFilter();
        $apiFilter->setInputStream($jsonTestFile);
        $param00 = new RequestParameter('json-array', 'array');
        $param00->setCustomFilterFunction(function($basicFiltered, $orgVal, RequestParameter $param){
            return $basicFiltered;
        });
        $apiFilter->addRequestParameter($param00);
        $this->assertEquals('array', $param00->getType());
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $filtered = $apiFilter->getInputs();
        $this->assertTrue($filtered instanceof Json);
        $this->assertEquals([], $filtered->get('json-array'));
        $this->assertFalse($filtered->hasKey('another-param'));
    }
    /**
     * @test
     */
    public function testFilterPost18() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, '{"json-array":[], "another-param":');
        $apiFilter = new APIFilter();
        $apiFilter->setInputStream($jsonTestFile);
        $param00 = new RequestParameter('json-array', 'array');
        $param00->setCustomFilterFunction(function($basicFiltered, $orgVal, RequestParameter $param){
            var_dump($orgVal);
            var_dump($basicFiltered);
        });
        $apiFilter->addRequestParameter($param00);
        $this->assertEquals('array', $param00->getType());
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->expectException('Exception');
        $apiFilter->filterPOST();
    }
    /**
     * @test
     */
    public function testFilterPost19() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, '{"json-array":[], "another-param":');
        $apiFilter = new APIFilter();
        $apiFilter->setInputStream($jsonTestFile);
        $param00 = new RequestParameter('json-array', 'array');
        $apiFilter->addRequestParameter($param00);
        $this->assertEquals('array', $param00->getType());
        putenv('REQUEST_METHOD=PUT');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->expectException('Exception');
        $apiFilter->filterPOST();
    }
    /**
     * @test
     */
    public function testFilterPost21() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "json-array":['
                . '        "hello",'
                . '        {'
                . '            "obj":true,'
                . '            "true":false'
                . '        }'
                . '    ],'
                . '    "another-arr":['
                . '        "sub-array",'
                . '        true,'
                . '        null'
                . '    ], '
                . '    "another-param":{'
                . '        "o":true,'
                . '        "arr":["one"]'
                . '    }'
                . '}');
        $apiFilter = new APIFilter();
        $apiFilter->setInputStream($jsonTestFile);
        $param00 = new RequestParameter('json-array', 'array');
        $apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('true', 'boolean');
        $apiFilter->addRequestParameter($param01);
        $param02 = new RequestParameter('arr', 'array');
        $apiFilter->addRequestParameter($param02);
        putenv('REQUEST_METHOD=PUT');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(3, count($json->getPropsNames()));
        $this->assertTrue($json->hasKey('json-array'));
        $this->assertTrue($json->hasKey('true'));
        $this->assertfalse($json->get('true'));
        $this->assertTrue($json->hasKey('arr'));
        $this->assertEquals(["one"], $json->get('arr'));
    }
    /**
     * @test
     */
    public function testFilterPost22() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "string":" My Super String"'
                . '}');
        $param00 = new RequestParameter('string');
        $param00->setCustomFilterFunction(function() {
            return '';
        });
        $apiFilter = new APIFilter();
        $apiFilter->addRequestParameter($param00);
        $apiFilter->setInputStream($jsonTestFile);
        putenv('REQUEST_METHOD=PUT');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(1, count($json->getPropsNames()));
        $this->assertTrue($json->hasKey('string'));
        $this->assertNull($json->get('string'));
    }
    /**
     * @test
     */
    public function testFilterPost23() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "number":" My Super String"'
                . '}');
        $param00 = new RequestParameter('number','integer');
        $param00->setDefault(500);
        $apiFilter = new APIFilter();
        $apiFilter->addRequestParameter($param00);
        $apiFilter->setInputStream($jsonTestFile);
        putenv('REQUEST_METHOD=PUT');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(1, count($json->getPropsNames()));
        $this->assertTrue($json->hasKey('number'));
        $this->assertEquals(500, $json->get('number'));
    }
    /**
     * @test
     */
    public function testFilterPost24() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "number":1,'
                . '    "another-number":1.5,'
                . '    "bool-true":true,'
                . '    "bool-false":false,'
                . '    "null-val":null,'
                . '    "sub-obj":{'
                . '        "array":["one","<script>Two</script>"]'
                . '    }'
                . '}');
        $apiFilter = new APIFilter();
        $param00 = new RequestParameter('number','integer');
        $apiFilter->addRequestParameter($param00);
        $param01= new RequestParameter('another-number','float');
        $apiFilter->addRequestParameter($param01);
        $param02 = new RequestParameter('bool-true','boolean');
        $apiFilter->addRequestParameter($param02);
        $param03 = new RequestParameter('bool-false','boolean');
        $apiFilter->addRequestParameter($param03);
        $param04 = new RequestParameter('array','array');
        $apiFilter->addRequestParameter($param04);
        $apiFilter->setInputStream($jsonTestFile);
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(5, count($json->getPropsNames()));
        $this->assertTrue($json->hasKey('number'));
        $this->assertEquals(1, $json->get('number'));
        $this->assertEquals(1.5, $json->get('another-number'));
        $this->assertTrue($json->get('bool-true'));
        $this->assertFalse($json->get('bool-false'));
        $this->assertEquals(["one","&lt;script&gt;Two&lt;/script&gt;"], $json->get('array'));
        $this->assertEquals(["one","<script>Two</script>"], $apiFilter->getNonFiltered()->get('array'));
    }
    /**
     * @test
     */
    public function testFilterPost25() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "obj-00":{'
                . '        "deep-obj":{'
                . '            "number":1'
                . '        }'
                . '    },'
                . '    "another-number":1.5,'
                . '    "bool-true":true,'
                . '    "bool-false":false,'
                . '    "null-val":null,'
                . '    "sub-obj":{'
                . '        "array":["one","<script>Two</script>", true, false, null, 1]'
                . '    },'
                . '    "json-obj":{'
                . '        "one":1,'
                . '        "two":2.5,'
                . '        "three":true,'
                . '        "four":["hell","no","<script>not clean</script>"]'
                . '    }'
                . '}');
        $apiFilter = new APIFilter();
        $param00 = new RequestParameter('number','integer');
        $apiFilter->addRequestParameter($param00);
        $param01= new RequestParameter('another-number','float');
        $apiFilter->addRequestParameter($param01);
        $param02 = new RequestParameter('bool-true','boolean');
        $apiFilter->addRequestParameter($param02);
        $param03 = new RequestParameter('bool-false','boolean');
        $apiFilter->addRequestParameter($param03);
        $param04 = new RequestParameter('array','array');
        $apiFilter->addRequestParameter($param04);
        $param05 = new RequestParameter('json-obj','json-obj');
        $apiFilter->addRequestParameter($param05);
        $apiFilter->setInputStream($jsonTestFile);
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(6, count($json->getPropsNames()));
        $this->assertTrue($json->hasKey('number'));
        $this->assertEquals(1, $json->get('number'));
        $this->assertEquals(1.5, $json->get('another-number'));
        $this->assertTrue($json->get('bool-true'));
        $this->assertFalse($json->get('bool-false'));
        $this->assertEquals(["one","&lt;script&gt;Two&lt;/script&gt;", true, false, null, 1], $json->get('array'));
        $jsonObj = $json->get('json-obj');
        $this->assertTrue($jsonObj instanceof Json);
        $this->assertEquals(["hell","no","<script>not clean</script>"], $jsonObj->get('four'));
    }
    /**
     * @test
     */
    public function testFilterPost26() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "json-obj":{'
                . '        "one":1,'
                . '        "two":2.5,'
                . '        "three":true,'
                . '        "four":["hell","no","<script>not clean</script>"]'
                . '    },'
                . '    "another-obj":"str"'
                . '}');
        $apiFilter = new APIFilter();
        $param00 = new RequestParameter('json-obj','json-obj');
        $param00->setCustomFilterFunction(function(Json $basicFiltered){
            $arr = $basicFiltered->get('four');
            $clean = [];
            foreach ($arr as $val) {
                $clean[] = strip_tags($val);
            }
            $basicFiltered->add('four', $clean);
            $basicFiltered->add('one', $basicFiltered->get('one')*2);
            $basicFiltered->add('two', $basicFiltered->get('two')*2);
            $basicFiltered->add('three', false);
            return $basicFiltered;
        });
        $apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('another-obj','json-obj');
        $apiFilter->addRequestParameter($param01);
        $apiFilter->setInputStream($jsonTestFile);
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(2, count($json->getPropsNames()));
        $this->assertNull($json->get('another-obj'));
        $jsonObj = $json->get('json-obj');
        $this->assertTrue($jsonObj instanceof Json);
        $this->assertEquals(["hell","no","not clean"], $jsonObj->get('four'));
        $this->assertEquals(2, $jsonObj->get('one'));
        $this->assertEquals(5, $jsonObj->get('two'));
        $this->assertFalse($jsonObj->get('three'));
    }
    /**
     * @test
     */
    public function testFilterPost27() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "json-obj":{'
                . '        "first-string":"<script>Not Safe.<?php",'
                . '        "two":{'
                . '            "second-string":"safe",'
                . '            "third-str":"",'
                . '            "obj":{'
                . '                "last-str":""'
                . '            }'
                . '        }'
                . '    }'
                . '}');
        $apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-string');
        $apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-string');
        $apiFilter->addRequestParameter($param01);
        $param02 = new RequestParameter('third-str');
        $apiFilter->addRequestParameter($param02);
        $param03 = new RequestParameter('last-str');
        $param03->setIsEmptyStringAllowed(true);
        $apiFilter->addRequestParameter($param03);
        
        $apiFilter->setInputStream($jsonTestFile);
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(4, count($json->getPropsNames()));
        $this->assertEquals('Not Safe.', $json->get('first-string'));
        $this->assertEquals('safe', $json->get('second-string'));
        $this->assertNull($json->get('third-str'));
        $this->assertEquals('', $json->get('last-str'));
        
        $nonClean = $apiFilter->getNonFiltered();
        $this->assertEquals('<script>Not Safe.<?php', $nonClean->get('first-string'));
        $this->assertEquals('safe', $nonClean->get('second-string'));
        $this->assertEquals('',$nonClean->get('third-str'));
        $this->assertEquals('', $nonClean->get('last-str'));
    }
    /**
     * @test
     */
    public function testFilterPost28() {
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setJsonInput($jsonTestFile, ''
                . '{'
                . '    "json-obj":{'
                . '        "invalid-param":77,'
                . '        "one":1,'
                . '        "sub-obj":{'
                . '            "arr":['
                . '                "with arr"'
                . '            ]'
                . '        },'
                . '        "array-of-arrays":['
                . '            ['
                . '                "hello",'
                . '                {'
                . '                    "obj":true,'
                . '                    "string":"<script>not safe"'
                . '                },'
                . '                ['
                . '                    "innerArr",'
                . '                    {'
                . '                        "sub-obj":{'
                . '                            "arr":['
                . '                                "with arr"'
                . '                            ]'
                . '                        }'
                . '                    },'
                . '                    [true],'
                . '                    [false]'
                . '                ]'
                . '            ]'
                . '        ]'
                . '    }'
                . '}');
        $apiFilter = new APIFilter();
        $param00 = new RequestParameter('array-of-arrays','array');
        $apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('sub-obj','json-obj');
        $apiFilter->addRequestParameter($param01);
        $param02 = new RequestParameter('with-default','integer');
        $param02->setDefault(44);
        $param02->setIsOptional(true);
        $apiFilter->addRequestParameter($param02);
        $param03 = new RequestParameter('invalid-param');
        $apiFilter->addRequestParameter($param03);
        
        $apiFilter->setInputStream($jsonTestFile);
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $apiFilter->filterPOST();
        $json = $apiFilter->getInputs();
        $this->assertEquals(4, count($apiFilter->getFilterDef()));
        $this->assertEquals([
            'array-of-arrays',
            'sub-obj',
            'with-default',
            'invalid-param'
        ], $json->getPropsNames());
        $this->assertEquals(44, $json->get('with-default'));
        $this->assertNull($json->get('invalid-param'));
        $this->assertEquals(1, count($json->get('array-of-arrays')));
        $this->assertEquals('hello', $json->get('array-of-arrays')[0][0]);
        $this->assertEquals('{"obj":true,"string":"&lt;script&gt;not safe"}', $json->get('array-of-arrays')[0][1]->toJSONString());
        $subObj = $json->get('sub-obj');
        $this->assertTrue($subObj instanceof Json);
        $this->assertEquals(['with arr'], $subObj->get('arr'));
    }
    public static function setJsonInput($fName, $jsonData) {
        $stream = fopen($fName, 'w+');
        fwrite($stream, $jsonData);
        fclose($stream);
    }
}






