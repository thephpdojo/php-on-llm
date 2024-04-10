<?php
use PHPOnLLM\Http\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {
    private $router;

    protected function setUp(): void {
        // Initialize router with a couple of routes
        $this->router = new Router([
            '/home' => function() { return 'Home Page'; },
            '/user/{id}' => function($id) { return 'User ' . $id; }
        ]);
    }

    public function testMatchExistingRoute() {
        // Simulate setting REQUEST_URI for the test
        $_SERVER['REQUEST_URI'] = '/home';

        $result = $this->router->match();

        $this->assertEquals('Home Page', $result);
    }

    public function testMatchRouteWithParameters() {
        $_SERVER['REQUEST_URI'] = '/user/123';

        $result = $this->router->match();

        $this->assertEquals('User 123', $result);
        // Check if parameters are correctly set
        $this->assertEquals(['id' => '123'], $_SERVER['ROUTE_PARAMS']);
    }

    public function testMatchNonExistingRoute() {
        $_SERVER['REQUEST_URI'] = '/non-existing';

        $result = $this->router->match();

        $this->assertNull($result);
    }

    public function testAddRoute() {
        $this->router->addRoute('/about', function() { return 'About Page'; });

        $_SERVER['REQUEST_URI'] = '/about';
        $result = $this->router->match();

        $this->assertEquals('About Page', $result);
    }
}
