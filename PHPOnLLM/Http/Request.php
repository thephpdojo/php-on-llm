<?php
namespace PHPOnLLM/Http;

class Request
{
    /**
     * The route parameters.
     *
     * @var array
     */
    protected $routeParams;

    /**
     * Request constructor.
     *
     * @param array $routeParams The route parameters.
     */
    public function __construct(array $routeParams = [])
    {
        $this->routeParams = $routeParams;
    }

    /**
     * Retrieve a query parameter value.
     *
     * @param string $key The query parameter key.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed
     */
    public function query(string $key, $default = null)
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * Retrieve a post parameter value.
     *
     * @param string $key The post parameter key.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed
     */
    public function post(string $key, $default = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * Retrieve a header from the request.
     *
     * @param string $key The header key.
     * @return string|null
     */
    public function header(string $key): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if the request is an AJAX request.
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Retrieve a server variable or all server variables.
     *
     * @param string|null $key The server variable key. If null, return all server variables.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed
     */
    public function server(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_SERVER;
        }

        $key = strtoupper($key);
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    /**
     * Retrieve a route parameter value.
     *
     * @param string $key The route parameter key.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed
     */
    public function route(string $key, $default = null)
    {
        return isset($this->routeParams[$key]) ? $this->routeParams[$key] : $default;
    }
}
