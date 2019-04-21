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
    public function setup() {
        $this->apiFilter = new APIFilter();
        echo "\nSetup Finshed\n";
    }
    /**
     * @test
     */
    public function testFilterGet00() {
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
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['username'] = 'Admin';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Admin',$filtered['username']);
    }
    /**
     * @test
     */
    public function testFilterGet02() {
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['username'] = '<script>alert(Admin)</script>';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('alert(Admin)',$filtered['username']);
    }
    /**
     * @test
     */
    public function testFilterGet03() {
        $param00 = new RequestParameter('username');
        $this->apiFilter->addRequestParameter($param00);
        $_GET['username'] = '<?php echo "<script>alert("Oh No!")</script>";?>Book';
        $this->apiFilter->filterGET();
        $filtered = $this->apiFilter->getInputs();
        $this->assertEquals(1,count($filtered));
        $this->assertTrue(isset($filtered['username']));
        $this->assertEquals('Book',$filtered['username']);
    }
    /**
     * @test
     */
    public function testFilterGet04() {
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
    }
    /**
     * @test
     */
    public function testFilterGet05() {
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
    }
}
