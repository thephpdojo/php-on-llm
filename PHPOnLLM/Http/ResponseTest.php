<?php
use PHPOnLLM\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    private $response;

    protected function setUp(): void
    {
        $this->response = new Response();
    }

    public function testSendHeader()
    {
        if (php_sapi_name() == 'cli') {
            $this->expectOutputString('');
            $this->response->sendHeader('Content-Type: text/plain');
        } else {
            // Note: In non-CLI environments, testing headers directly is tricky
            // because they are sent to the browser. You may need to mock or use
            // output buffering to capture headers.
            $this->markTestIncomplete('Header sending cannot be tested in non-CLI mode.');
        }
    }

    public function testFileNotFound()
    {
        $this->expectOutputString('');
        $this->response->file('/path/to/nonexistent/file.txt');
    }

    public function testFile()
    {
        // For actual file serving test, we need to create a temporary file
        $tempFile = tmpfile();
        fwrite($tempFile, 'Hello World');
        $metaData = stream_get_meta_data($tempFile);
        $tempFilePath = $metaData['uri'];

        $this->expectOutputString('Hello World');
        $this->response->file($tempFilePath);
        fclose($tempFile); // Close the file to clean up
    }

    public function testJson()
    {
        $this->expectOutputString(json_encode(['key' => 'value']));
        $this->response->json(['key' => 'value']);
    }
}
