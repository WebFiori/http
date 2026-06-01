<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\Request;
use WebFiori\Http\RequestMethod;

/**
 * Tests for Request::parsePutPatchBody().
 * 
 * Verifies that PUT/PATCH body parsing (previously in WebServicesManager::populatePutData)
 * now works correctly in the Request class.
 * 
 * @see https://github.com/WebFiori/http/issues/118
 */
class RequestPutPatchParsingTest extends TestCase {

    private array $globalsBackup;

    protected function setUp(): void {
        parent::setUp();
        $this->globalsBackup = [
            'POST' => $_POST,
            'FILES' => $_FILES,
            'SERVER' => $_SERVER,
        ];
    }

    protected function tearDown(): void {
        $_POST = $this->globalsBackup['POST'];
        $_FILES = $this->globalsBackup['FILES'];
        $_SERVER = $this->globalsBackup['SERVER'];
        parent::tearDown();
    }

    public function testParseUrlEncodedPutBody() {
        $_POST = [];
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $request = new Request();
        $request->setRequestMethod(RequestMethod::PUT);
        $request->setBody('name=John&age=30&email=john%40example.com');

        $request->parsePutPatchBody();

        $this->assertEquals('John', $_POST['name']);
        $this->assertEquals('30', $_POST['age']);
        $this->assertEquals('john@example.com', $_POST['email']);
    }

    public function testParseUrlEncodedPatchBody() {
        $_POST = [];
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $request = new Request();
        $request->setRequestMethod(RequestMethod::PATCH);
        $request->setBody('status=active&priority=high');

        $request->parsePutPatchBody();

        $this->assertEquals('active', $_POST['status']);
        $this->assertEquals('high', $_POST['priority']);
    }

    public function testParseMultipartPutBody() {
        $_POST = [];
        $_FILES = [];
        $boundary = '----WebKitFormBoundary7MA4YWxkTrZu0gW';
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data; boundary=' . $boundary;
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $body = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
            . "Content-Disposition: form-data; name=\"title\"\r\n\r\n"
            . "My Document\r\n"
            . "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
            . "Content-Disposition: form-data; name=\"description\"\r\n\r\n"
            . "A test document\r\n"
            . "------WebKitFormBoundary7MA4YWxkTrZu0gW--\r\n";

        $request = new Request();
        $request->setRequestMethod(RequestMethod::PUT);
        $request->setBody($body);

        $request->parsePutPatchBody();

        $this->assertEquals('My Document', $_POST['title']);
        $this->assertEquals('A test document', $_POST['description']);
    }

    public function testParseMultipartWithFileUpload() {
        $_POST = [];
        $_FILES = [];
        $boundary = '----TestBoundary123';
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data; boundary=' . $boundary;
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $fileContent = 'Hello, this is file content.';
        $body = "------TestBoundary123\r\n"
            . "Content-Disposition: form-data; name=\"name\"\r\n\r\n"
            . "John\r\n"
            . "------TestBoundary123\r\n"
            . "Content-Disposition: form-data; name=\"avatar\"; filename=\"photo.png\"\r\n"
            . "Content-Type: image/png\r\n\r\n"
            . $fileContent . "\r\n"
            . "------TestBoundary123--\r\n";

        $request = new Request();
        $request->setRequestMethod(RequestMethod::PUT);
        $request->setBody($body);

        $request->parsePutPatchBody();

        $this->assertEquals('John', $_POST['name']);
        $this->assertArrayHasKey('avatar', $_FILES);
        $this->assertEquals('photo.png', $_FILES['avatar']['name']);
        $this->assertEquals('image/png', $_FILES['avatar']['type']);
        $this->assertEquals(UPLOAD_ERR_OK, $_FILES['avatar']['error']);
        $this->assertEquals(strlen($fileContent), $_FILES['avatar']['size']);
        $this->assertFileExists($_FILES['avatar']['tmp_name']);
        $this->assertEquals($fileContent, file_get_contents($_FILES['avatar']['tmp_name']));

        // Cleanup temp file
        @unlink($_FILES['avatar']['tmp_name']);
    }

    public function testEmptyBodyDoesNothing() {
        $_POST = [];
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';

        $request = new Request();
        $request->setRequestMethod(RequestMethod::PUT);
        $request->setBody('');

        $request->parsePutPatchBody();

        $this->assertEmpty($_POST);
    }

    public function testJsonContentTypeNotParsed() {
        $_POST = [];
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $request = new Request();
        $request->setRequestMethod(RequestMethod::PUT);
        $request->setBody('{"name":"John"}');

        $request->parsePutPatchBody();

        // JSON bodies are handled by APIFilter, not here
        $this->assertEmpty($_POST);
    }

    public function testMultipartWithoutBoundaryDoesNothing() {
        $_POST = [];
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';

        $request = new Request();
        $request->setRequestMethod(RequestMethod::PUT);
        $request->setBody('some data');

        $request->parsePutPatchBody();

        $this->assertEmpty($_POST);
    }
}
