<?php
namespace restEasy\tests;
use PHPUnit\Framework\TestCase;
use restEasy\RequestParameter;
use restEasy\APIFilter;
/**
 * Description of APIFilterTest
 *
 * @author Eng.Ibrahim
 */
class APIFilterTest extends TestCase{
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
        $param01->setMinVal(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['username'] = 'Admin';
        $_GET['password'] = '100';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Admin',$filtered['username']);
        $this->assertTrue(isset($filtered['password']));
        $this->assertEquals('INV',$filtered['password']);
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
        $param01->setMinVal(1000000);
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
        $param01->setMinVal(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['username'] = 'Admin';
        $_GET['password'] = '1002000with some text<script></script>';
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
        $this->assertEquals('1002000with some text<script></script>',$nonFiltered['password']);
    }
    /**
     * @test
     */
    public function testFilterGet07() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('first-number','float');
        $this->apiFilter->addRequestParameter($param00);
        $param01 = new RequestParameter('second-number', 'float');
        $param01->setMinVal(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = 'Admin';
        $_GET['second-number'] = 'yc with some text<script></script>';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['first-number']));
        $this->assertEquals('INV',$filtered['first-number']);
        $this->assertTrue(isset($filtered['second-number']));
        $this->assertEquals('INV',$filtered['second-number']);
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
        $param01->setMinVal(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = 'Admin';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        echo "testFilterGet08\n";
        print_r($filtered);
        if(PHP_MAJOR_VERSION == 5){
            $this->assertEquals(1,count($filtered));
            $this->assertTrue(isset($filtered['first-number']));
            $this->assertEquals('INV',$filtered['first-number']);
            $this->assertFalse(isset($filtered['second-number']));
            $nonFiltered = $this->apiFilter->getNonFiltered();
            $this->assertTrue(isset($nonFiltered['first-number']));
            $this->assertEquals('Admin',$nonFiltered['first-number']);
            $this->assertFalse(isset($nonFiltered['second-number']));
        }
        else{
            $this->assertEquals(2,count($filtered));
            $this->assertTrue(isset($filtered['first-number']));
            $this->assertEquals('INV',$filtered['first-number']);
            $this->assertTrue(isset($filtered['second-number']));
            $nonFiltered = $this->apiFilter->getNonFiltered();
            $this->assertTrue(isset($nonFiltered['first-number']));
            $this->assertEquals('Admin',$nonFiltered['first-number']);
            $this->assertFalse(isset($nonFiltered['second-number']));
        }
        
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
        $param01->setMinVal(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['first-number'] = 'Admin';
        $_GET['second-number'] = '';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(2,count($filtered));
        $this->assertTrue(isset($filtered['first-number']));
        $this->assertEquals('INV',$filtered['first-number']);
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
        $param01->setMinVal(1000000);
        $this->apiFilter->addRequestParameter($param01);
        $_GET['second-number'] = 'this is a 100076800 year .';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        echo "testFilterGet10\n";
        print_r($filtered);
        $this->assertEquals(2,count($filtered));
        $this->assertTrue($filtered['first-number'] === null);
        $this->assertTrue(isset($filtered['second-number']));
        $this->assertEquals(100076800,$filtered['second-number']);
        
        $nonFiltered = $this->apiFilter->getNonFiltered();
        $this->assertNull($nonFiltered['first-number']);
        $this->assertTrue(isset($nonFiltered['second-number']));
        $this->assertEquals('this is a 100076800 year .',$nonFiltered['second-number']);
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
        $this->assertEquals('INV',$filtered['my-string']);
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
        $this->assertEquals('INV',$filtered['redirect']);
    }
    /**
     * @test
     */
    public function testFilterGet14() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('redirect', 'url');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['redirect'] = 'programmingacademia.com';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals('INV',$filtered['redirect']);
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
        $this->assertEquals('INV',$filtered['send-to']);
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
        $this->assertEquals('INV',$filtered['send-to']);
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
        $param00->setCustomFilterFunction(function($val,$reqParam){
            $original = $val['basic-filter-result'];
            return 'Email to: '.$original;
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
    public function testFilterPost01() {
        $this->apiFilter = new APIFilter();
        $param00 = new RequestParameter('array', 'array');
        $this->apiFilter->addRequestParameter($param00);
        $_POST['array'] = '["hello"]';
        $this->apiFilter->filterPOST();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertEquals(array("hello"),$filtered['array']);
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
        $this->assertEquals(array(1,2,6.9,8.9,99,-80),$filtered['array']);
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
        $this->assertEquals(array(true,false,null,"A , string"),$filtered['array']);
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
        $this->assertEquals(array("Ooooh! it feels good!",-9986.8,true,779,false,"A,Wrld, Is 55 imes, good. Is't",null,-88.9,90,"A , string"),$filtered['array']);
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
        $this->assertEquals(array("Ooooh! it feels good!",-9986.8,true,779,false,"A,Wrld, alert('Hello') Is 55 imes, good. Is't",null,-88.9,90,"A , string"),$filtered['array']);
    }
}
