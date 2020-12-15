<?php
namespace webfiori\tests\entity\router;

use PHPUnit\Framework\TestCase;
use webfiori\http\Uri;
/**
 *
 * @author Ibrahim
 */
class UriTest extends TestCase {
    /**
     * @test
     */
    public function testSetUriPossibleVar00() {
        $uri = new Uri('https://example.com/{first-var}', '');
        $uri->addVarValue('first-var', 'Hello World');
        $this->assertEquals(['Hello World'], $uri->getVarValues('first-var'));
        $this->assertEquals('/{first-var}', $uri->getPath());
        $this->assertEquals(['{first-var}'], $uri->getPathArray());
    }
    /**
     * @test
     */
    public function testSetUriPossibleVar01() {
        $uri = new Uri('https://example.com/{first-var}', '');
        $uri->addVarValue('  first-var  ', '  Hello World  ');
        $this->assertEquals(['Hello World'], $uri->getVarValues('first-var'));
    }
    /**
     * @test
     */
    public function testSetUriPossibleVar02() {
        $uri = new Uri('https://example.com/{first-var}', '');
        $uri->addVarValues('first-var', ['Hello','World']);
        $this->assertEquals(['Hello','World'], $uri->getVarValues('first-var'));
    }
    /**
     * @test
     */
    public function testSetUriPossibleVar03() {
        $uri = new Uri('https://example.com/{first-var}/ok/{second-var}', '');
        $uri->addVarValues('first-var', ['Hello','World']);
        $uri->addVarValues('  second-var ', ['hell','is','not','heven']);
        $uri->addVarValues('  secohhnd-var ', ['hell','is']);
        $this->assertEquals(['Hello','World'], $uri->getVarValues('first-var'));
        $this->assertEquals(['hell','is','not','heven'], $uri->getVarValues('second-var'));
        $this->assertEquals([], $uri->getVarValues('secohhnd-var'));
    }
    /**
     * @test
     */
    public function testInvalid00() {
        $this->expectException('Exception');
        $uri = new Uri('', '');
    }
    /**
     * @test
     */
    public function testEquals00() {
        $uri1 = new Uri('https://example.com/my-folder', '');
        $uri2 = new Uri('https://example.com/my-folder', '');
        $this->assertTrue($uri1->equals($uri2));
        $this->assertTrue($uri2->equals($uri1));
    }
    /**
     * @test
     */
    public function testEquals01() {
        $uri1 = new Uri('https://example.com:80/my-folder', '');
        $uri2 = new Uri('https://example.com/my-folder', '');
        $this->assertFalse($uri1->equals($uri2));
        $this->assertFalse($uri2->equals($uri1));
    }
    /**
     * @test
     */
    public function testEquals02() {
        $uri1 = new Uri('http://example.com/my-folder-2', '');
        $uri2 = new Uri('https://example.com/my-folder', '');
        $this->assertFalse($uri1->equals($uri2));
        $this->assertFalse($uri2->equals($uri1));
    }
    /**
     * @test
     */
    public function testEquals03() {
        $uri1 = new Uri('http://example.com/my-folder', '');
        $uri2 = new Uri('https://example.com/my-folder', '');
        $this->assertTrue($uri1->equals($uri2));
        $this->assertTrue($uri2->equals($uri1));
    }
    /**
     * @test
     */
    public function testEquals04() {
        $uri1 = new Uri('http://example.com/my-folder/{a-var}', '');
        $uri2 = new Uri('https://example.com/my-folder/{a-var}', '');
        $this->assertTrue($uri1->equals($uri2));
        $this->assertTrue($uri2->equals($uri1));
    }
    /**
     * @test
     */
    public function testGetComponents() {
        $uri = new Uri('https://example.com:8080/hell?me=ibrahim#22', '');
        $components = $uri->getComponents();
        $this->assertEquals('https://example.com:8080/hell?me=ibrahim#22', $components['uri']);
        $this->assertEquals('https',$components['scheme']);
        $this->assertEquals('//example.com:8080', $components['authority']);
        $this->assertEquals(8080, $components['port']);
        $this->assertEquals('22', $components['fragment']);
        $this->assertEquals(['hell'], $components['path']);
    }
    /**
     * @test
     */
    public function testEquals06() {
        $uri1 = new Uri('http://example.com/my-Folder/{a-var}', '', false);
        $uri2 = new Uri('https://example.com/my-folder/{a-var}', '', false);
        $this->assertFalse($uri1->equals($uri2));
        $this->assertFalse($uri2->equals($uri1));
    }
    /**
     * @test
     */
    public function testSplitURI_02() {
        $uri = 'https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}/?do=dnt&y=#xyz';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('80',$uriObj->getPort());
    }
    /**
     * @test
     */
    public function testSplitURI_03() {
        $uri = 'https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}/?do=dnt&y=#xyz';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('xyz',$uriObj->getFragment());
    }
    /**
     * @test
     */
    public function testSplitURI_04() {
        $uri = 'https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}/?do=dnt&y=#xyz';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('do=dnt&y=',$uriObj->getQueryString());
    }
    /**
     * @test
     */
    public function testSplitURI_05() {
        $uri = 'https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}/?do=dnt&y=#xyz';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('https',$uriObj->getScheme());
    }
    /**
     * @test
     */
    public function testSplitURI_06() {
        $uri = 'https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}/?do=dnt&y=#xyz';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/{some-var}/hell/{other-var}',$uriObj->getPath());
        $queryStrVars = $uriObj->getQueryStringVars();
        $this->assertEquals(2,count($queryStrVars));
        $this->assertEquals('dnt',$queryStrVars['do']);
        $this->assertEquals('',$queryStrVars['y']);
        $this->assertEquals('www3.programmingacademia.com',$uriObj->getHost());
        $this->assertEquals('https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}',$uriObj->getUri());
        $this->assertEquals('https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}?do=dnt&y=',$uriObj->getUri(true));
        $this->assertEquals('https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}#xyz',$uriObj->getUri(false,true));
        $this->assertEquals('https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}?do=dnt&y=#xyz',$uriObj->getUri(true,true));
    }
    /**
     * @test
     */
    public function testSplitURI_07() {
        $uri = 'https://www3.programmingacademia.com:80/{some-var}/{x}/{some-var}';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/{some-var}/{x}/{some-var}',$uriObj->getPath());
        $this->assertEquals(2,count($uriObj->getUriVars()));
    }
    /**
     * @test
     */
    public function testSplitURI_08() {
        $uri = 'https://programmingacademia.com/Hello World';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/Hello World',$uriObj->getPath());
    }
    /**
     * @test
     */
    public function testSplitURI_09() {
        $uri = 'https://programmingacademia.com/Hello World? or Not?';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/Hello World? or Not?',$uriObj->getPath());
    }
    /**
     * @test
     */
    public function testSplitURI_10() {
        $uri = 'https://programmingacademia.com/Hello World? or Not?Yes';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/Hello World? or Not',$uriObj->getPath());
    }
    /**
     * @test
     */
    public function testSplitURI_11() {
        $uri = 'https://programmingacademia.com/Hello World#or Not#Yes';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/Hello World#or Not',$uriObj->getPath());
        $this->assertEquals('Yes',$uriObj->getFragment());
    }
}
