<?php
namespace webfiori\tests\http;

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
    public function testAllowedRequestMethods00() {
        putenv('REQUEST_METHOD=GET');
        $uri = new Uri('https://example.com/', '');
        $this->assertEquals([], $uri->getRequestMethods());
        $this->assertTrue($uri->isRequestMethodAllowed());
        $uri->addRequestMethod('GET');
        $this->assertEquals(['GET'], $uri->getRequestMethods());
        $this->assertTrue($uri->isRequestMethodAllowed());
    }
    /**
     * @test
     */
    public function testAllowedRequestMethods01() {
        putenv('REQUEST_METHOD=GET');
        $uri = new Uri('https://example.com/', '');
        $uri->setRequestMethods(['POST', 'PUT', 'Get']);
        $this->assertEquals(['POST', 'PUT'], $uri->getRequestMethods());
        $this->assertFalse($uri->isRequestMethodAllowed());
        putenv('REQUEST_METHOD=PUT');
        $this->assertTrue($uri->isRequestMethodAllowed());
    }
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
    public function testParams00() {
        $uri = new Uri('https://example.com/{first-var}');
        $this->assertTrue($uri->hasParameter('first-var'));
        $this->assertFalse($uri->getParameter('first-var')->isOptional());
        $this->assertNull($uri->getParameter('first-var')->getValue());
        $uri->setParameterValue('first-var', '1009');
        $this->assertEquals('1009', $uri->getParameter('first-var')->getValue());
    }
    /**
     * @test
     */
    public function testParams01() {
        $uri = new Uri('https://example.com/{first-var?}');
        $this->assertEquals('/{first-var?}', $uri->getPath());
        $this->assertTrue($uri->hasParameter('first-var'));
        $this->assertTrue($uri->getParameter('first-var')->isOptional());
        $this->assertNull($uri->getParameter('first-var')->getValue());
        $uri->setParameterValue('first-var', '1009');
        $this->assertEquals(['first-var'], $uri->getParametersNames());
        $this->assertEquals('1009', $uri->getParameter('first-var')->getValue());
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
    public function testGetBase00() {
        $this->assertEquals('http://127.0.0.1', Uri::getBaseURL());
    }
    /**
     * @test
     */
    public function testGetBase01() {
        $_SERVER['HTTP_HOST'] = 'webfiori.com';
        $this->assertEquals('http://webfiori.com', Uri::getBaseURL());
    }
    /**
     * @test
     */
    public function testGetBase03() {
        $_SERVER['HTTP_HOST'] = 'webfiori.com';
        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
        define('WF_PATH_TO_APPEND', 'my-app');
        $this->assertEquals('http://webfiori.com/my-app', Uri::getBaseURL());
    }
    /**
     * @test
     */
    public function testGetBase04() {
        $_SERVER['HTTP_HOST'] = 'webfiori.com';
        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
        $_SERVER['HTTPS'] = 'HTTPS';
        $this->assertEquals('https://webfiori.com/my-app', Uri::getBaseURL());
    }
    /**
     * @test
     */
    public function testGetBase02() {
        $_SERVER['HTTP_HOST'] = 'webfiori.com';
        $_SERVER['DOCUMENT_ROOT'] = __DIR__;
        $_SERVER['HTTPS'] = null;
        define('WF_PATH_TO_REMOVE', 'my-app');
        $this->assertEquals('http://webfiori.com/my-app', Uri::getBaseURL());
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
        $this->assertEquals(2,count($uriObj->getParameters()));
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
        $uri = 'https://programmingacademia.com/Hello World? /or Not?super?';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/Hello World? /or Not?super?',$uriObj->getPath());
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
    /**
     * @test
     */
    public function testSplitURI_12() {
        $uri = 'https://programmingacademia.com/{some-var?}/ok/not/super?one=2';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/{some-var?}/ok/not/super',$uriObj->getPath());
        $this->assertEquals('one=2',$uriObj->getQueryString());
        $this->assertTrue($uriObj->hasParameter('some-var'));
    }
    /**
     * @test
     */
    public function testSplitURI_13() {
        $uri = 'https://programmingacademia.com/{some-var?}/{another?}/not/super';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/{some-var?}/{another?}/not/super',$uriObj->getPath());
        $this->assertTrue($uriObj->hasParameter('some-var'));
        $this->assertTrue($uriObj->hasParameter('another'));
        $this->assertTrue($uriObj->isAllParametersSet());
    }
    /**
     * @test
     */
    public function testSplitURI_14() {
        $uri = 'https://programmingacademia.com/{some-var?}/{another}/not/super';
        $uriObj = new Uri($uri, '');
        $this->assertEquals('/{some-var?}/{another}/not/super',$uriObj->getPath());
        $this->assertTrue($uriObj->hasParameter('some-var'));
        $this->assertTrue($uriObj->hasParameter('another'));
        $this->assertFalse($uriObj->isAllParametersSet());
    }
    /**
     * @test
     */
    public function testSplitURI_15() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Empty string not allowed as variable name.');
        $uri = 'https://programmingacademia.com/{}/{another}/not/super';
        $uriObj = new Uri($uri, '');
    }
    /**
     * @test
     */
    public function testSplitURI_16() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Empty string not allowed as variable name.');
        $uri = 'https://programmingacademia.com/{?}/{another}/not/super';
        $uriObj = new Uri($uri, '');
    }
}
