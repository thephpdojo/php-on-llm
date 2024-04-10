<?php

namespace PHPOnLLM\Http;

require_once 'Request.php'; // Assume your Request class is defined in this file

class RequestTest
{
    public function testJsonMethodReturnsNullForNonPostRequests()
    {
        // Simulate a non-POST request
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = new Request();
        $result = $request->json();

        if ($result === null) {
            echo "testJsonMethodReturnsNullForNonPostRequests: PASSED\n";
        } else {
            echo "testJsonMethodReturnsNullForNonPostRequests: FAILED\n";
        }
    }

    public function testJsonMethodReturnsArrayForPostRequestsWithoutKey()
    {
        // Simulate a POST request and JSON payload
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $testJsonData = json_encode(['name' => 'John Doe']);

        $request = new Request();
        $request->setRawPostData($testJsonData);
        $result = $request->json();

        if (is_array($result) && $result['name'] === 'John Doe') {
            echo "testJsonMethodReturnsArrayForPostRequestsWithoutKey: PASSED\n";
        } else {
            echo "testJsonMethodReturnsArrayForPostRequestsWithoutKey: FAILED\n";
        }
    }

    public function testJsonMethodReturnsNestedValueForPostRequests()
    {
        // Simulate a POST request and JSON payload
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $testJsonData = json_encode(['parentKey' => ['childKey' => 'value']]);

        $request = new Request();
        $request->setRawPostData($testJsonData);
        $result = $request->json('parentKey.childKey');

        if ($result === 'value') {
            echo "testJsonMethodReturnsNestedValueForPostRequests: PASSED\n";
        } else {
            echo "testJsonMethodReturnsNestedValueForPostRequests: FAILED\n";
        }
    }

    public function run()
    {
        $this->testJsonMethodReturnsNullForNonPostRequests();
        $this->testJsonMethodReturnsArrayForPostRequestsWithoutKey();
        $this->testJsonMethodReturnsNestedValueForPostRequests();
    }
}

// Running the tests
$test = new RequestTest();
$test->run();
