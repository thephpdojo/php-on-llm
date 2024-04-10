<?php

namespace PHPOnLLM\Http;

require 'Response.php'; // Adjust the path as needed

class ResponseTest
{
    public function testJson()
    {
        $response = new Response();
        $data = ['message' => 'Test'];

        ob_start();
        $response->json($data);
        $output = ob_get_clean();

        $expected = json_encode($data);
        if ($output === $expected) {
            echo "testJson passed\n";
        } else {
            echo "testJson failed\n";
        }
    }

    public function testFile()
    {
        // Create a temporary file and write content to it
        $filePath = '/tmp/testfile.txt';
        $fileContent = 'This is a test.';
        file_put_contents($filePath, $fileContent);

        $response = new Response();

        ob_start();
        $response->file($filePath);
        $output = ob_get_clean();

        // Delete the temporary file
        unlink($filePath);

        if ($output === $fileContent) {
            echo "testFile passed\n";
        } else {
            echo "testFile failed\n";
        }
    }
}

// Running the tests
$test = new ResponseTest();
$test->testJson();
$test->testFile();
