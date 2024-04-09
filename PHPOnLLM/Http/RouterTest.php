<?php

namespace PHPOnLLM\Http;

require 'Router.php';  // Make sure this path is correct for the Router class

class RouterTest {
    private $router;

    public function __construct() {
        $this->router = new Router();

        // Define handlers
        $this->router->addRoute('/user/{id}', [$this, 'userProfileHandler']);
        $this->router->addRoute('/user/create', [$this, 'userCreateHandler']);
    }

    public function runTests() {
        $this->testRouter('/user/123', "UserProfileHandler for user 123");
        $this->testRouter('/user/create', "UserCreateHandler");
        $this->testRouter('/nonexistent', null);
    }

    private function testRouter($path, $expectedResult) {
        $_SERVER['REQUEST_URI'] = $path;
        $result = $this->router->match();

        if ((is_callable($result) && call_user_func($result) === $expectedResult) || $result === $expectedResult) {
            echo "PASS: Test for path '$path'\n";
        } else {
            echo "FAIL: Test for path '$path'. Expected '$expectedResult', got '" . print_r($result, true) . "'\n";
        }
    }

    public function userProfileHandler($params) {
        return "UserProfileHandler for user " . $params['id'];
    }

    public function userCreateHandler() {
        return "UserCreateHandler";
    }
}

// Usage
$test = new \PHPOnLLM\Http\RouterTest();
$test->runTests();
