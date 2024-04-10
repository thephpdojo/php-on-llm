<?php
use PHPOnLLM\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private $request;

    protected function setUp(): void
    {
        $_GET = ['queryKey' => 'queryValue'];
        $_POST = ['postKey' => 'postValue'];
        $_SERVER = [
            'REQUEST_METHOD' => 'POST',
            'HTTP_X_REQUESTED_WITH' => 'xmlhttprequest',
            'HTTP_TEST_HEADER' => 'headerValue'
        ];

        $this->request = new Request(['routeKey' => 'routeValue']);
        $this->request->setRawPostData('{"jsonKey": "jsonValue"}');
    }

    public function testQuery()
    {
        $this->assertEquals('queryValue', $this->request->query('queryKey'));
        $this->assertNull($this->request->query('nonExistingKey'));
    }

    public function testPost()
    {
        $this->assertEquals('postValue', $this->request->post('postKey'));
        $this->assertNull($this->request->post('nonExistingKey'));
    }

    public function testJson()
    {
        $this->assertEquals(['jsonKey' => 'jsonValue'], $this->request->json());
        $this->assertEquals('jsonValue', $this->request->json('jsonKey'));
        $this->assertNull($this->request->json('nonExistingKey'));
    }

    public function testHeader()
    {
        $this->assertEquals('headerValue', $this->request->header('Test-Header'));
        $this->assertNull($this->request->header('Non-Existing-Header'));
    }

    public function testMethod()
    {
        $this->assertEquals('POST', $this->request->method());
    }

    public function testIsAjax()
    {
        $this->assertTrue($this->request->isAjax());
    }

    public function testServer()
    {
        $this->assertEquals('xmlhttprequest', $this->request->server('HTTP_X_REQUESTED_WITH'));
        $this->assertNull($this->request->server('NON_EXISTING_KEY'));
        $this->assertIsArray($this->request->server());
    }

    public function testRoute()
    {
        $this->assertEquals('routeValue', $this->request->route('routeKey'));
        $this->assertNull($this->request->route('nonExistingKey'));
    }
}
