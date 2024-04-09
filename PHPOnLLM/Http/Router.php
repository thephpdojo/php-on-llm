<?php
/**
Prompt: chatgpt-4
please construct a Router class in php, it will take in an array of route patterns and then match
the incoming http request with the route patterns
please use the similar technique as FastRoute

for the addRoute and match, just focus on request route matching, no need to consider matching the request method

please speed up the match method by optimizing the regex matching
 */
namespace PHPOnLLM/Http;

class Router {
    private $routes = [];

    public function __construct($routes = []) {
        foreach ($routes as $pattern => $handler) {
            $this->addRoute($pattern, $handler);
        }
    }

    public function addRoute($pattern, $handler) {
        $regex = $this->patternToRegex($pattern);
        // Pre-compile regex and store handler and regex
        $this->routes[] = [
            'regex' => $regex,
            'handler' => $handler
        ];
    }

    public function match($requestUri) {
        // Automatically fetch the request URI
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $requestUri, $matches)) {
                // Assuming the handler is a callable function or method
                if (is_callable($route['handler'])) {
                    // Extract named parameters
                    $params = [];
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $params[$key] = $value;
                        }
                    }

                    // Call the handler function with the parameters
                    return call_user_func($route['handler'], $params);
                }
                return $route['handler']; // If not callable, just return the handler info
            }
        }

        return null; // No matching route found
    }

    private function patternToRegex($pattern) {
        $pattern = preg_replace('#\{(\w+)\}#', '(?<$1>[^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }
}
