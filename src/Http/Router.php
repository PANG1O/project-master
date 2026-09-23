<?php
declare(strict_types=1);

namespace PHPCore\Core\Http;

use PHPCore\Core\Infrastructure\Logger;
use PHPCore\Core\System\Config;
use Throwable;

/**
 * Provides routing functionality for HTTP requests, including route matching, controller resolution, middleware
 * execution, and dynamic route parameters.
 *
 * @author Julius Derigs <julius@derigs.top
 */
class Router
{
    ####################################################################################################################
    # --- PROPERTIES ------------------------------------------------------------------------------------------------- #
    ####################################################################################################################

    /**
     * Registered routes.
     *
     * @var Route[]
     */
    private static array $routes = [];

    ####################################################################################################################
    # --- PUBLIC METHODS --------------------------------------------------------------------------------------------- #
    ####################################################################################################################

    /**
     * Registers a GET route.
     *
     * @param string $path
     * @param string $action
     * @return Route
     */
    public static function get(string $path, string $action): Route
    {
        return self::add('GET', $path, $action);
    }

    /**
     * Registers a POST route.
     *
     * @param string $path
     * @param string $action
     * @return Route
     */
    public static function post(string $path, string $action): Route
    {
        return self::add('POST', $path, $action);
    }

    /**
     * Registers a PUT route.
     *
     * @param string $path
     * @param string $action
     * @return Route
     */
    public static function put(string $path, string $action): Route
    {
        return self::add('PUT', $path, $action);
    }

    /**
     * Registers a PATCH route.
     *
     * @param string $path
     * @param string $action
     * @return Route
     */
    public static function patch(string $path, string $action): Route
    {
        return self::add('PATCH', $path, $action);
    }

    /**
     * Registers a DELETE route.
     *
     * @param string $path
     * @param string $action
     * @return Route
     */
    public static function delete(string $path, string $action): Route
    {
        return self::add('DELETE', $path, $action);
    }

    /**
     * Resolves the current HTTP request against all registered routes.
     *
     * @return void
     */
    public static function run(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $startTime = microtime(true);
        $path = self::getRequestPath();

        try {
            $requestMethod = strtoupper($method);
            $requestPath = self::getRequestPath();

            foreach (self::$routes as $route) {
                $parameters = self::matchRoute(
                    $route->getPath(),
                    $requestPath
                );

                if ($parameters === false) {
                    continue;
                }

                if ($route->getMethod() !== $requestMethod) {
                    http_response_code(405);

                    echo '405 Method Not Allowed';

                    exit();
                }

                self::executeRoute($route, $parameters);
            }

            Logger::log('dev', 'End');

            return;
        } catch (Throwable $exception) {
            http_response_code(500);

            Logger::log('error', '[Router::run()] ' . $exception->getMessage());
        } finally {
            // Insert your code to be run after routing is done. (e.g. API Access Audits)
        }
    }

    /**
     * Builds a URL out of the configured base URL and the given URI.
     *
     * @param string $uri
     * @return string
     */
    public static function baseURL(string $uri = ''): string
    {
        $baseURL = $_ENV['BASE_URL'] ?? Config::get('app.baseURL');

        if ($uri === '') {
            return rtrim($baseURL, '/');
        }

        return rtrim($baseURL, '/') . '/' . ltrim($uri, '/');
    }

    ####################################################################################################################
    # --- PRIVATE METHODS -------------------------------------------------------------------------------------------- #
    ####################################################################################################################

    /**
     * Registers a new route.
     *
     * @param string $method
     * @param string $path
     * @param string $action
     * @return Route
     */
    private static function add(
        string $method,
        string $path,
        string $action
    ): Route {
        if (!str_contains($action, '@')) {
            throw new \InvalidArgumentException(
                'Route action must contain a controller and method separated by "@".'
            );
        }

        [$controller, $controllerMethod] = explode('@', $action, 2);

        $route = new Route(
            $method,
            $path,
            $controller,
            $controllerMethod
        );

        self::$routes[] = $route;

        return $route;
    }

    /**
     * Returns the current request path without query parameters or the configured base URL.
     *
     * @return string
     */
    private static function getRequestPath(): string
    {
        $path = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        $baseURL = parse_url(
            self::baseURL(),
            PHP_URL_PATH
        );

        $path = trim($path ?? '', '/');
        $baseURL = trim($baseURL ?? '', '/');

        if ($baseURL !== '') {
            if ($path === $baseURL) {
                return '';
            }

            if (str_starts_with($path, $baseURL . '/')) {
                $path = substr(
                    $path,
                    strlen($baseURL) + 1
                );
            }
        }

        return $path;
    }

    /**
     * Matches a request path against a route pattern and returns its parameters.
     *
     * Dynamic parameters use the {parameter} syntax.
     *
     * @param string $route
     * @param string $requestPath
     * @return array|false
     */
    private static function matchRoute(
        string $route,
        string $requestPath
    ): array|false {
        $route = trim($route, '/');
        $requestPath = trim($requestPath, '/');

        /*
         * Extract parameter names.
         */
        preg_match_all(
            '/\{([^}]+)}/',
            $route,
            $parameterMatches
        );

        /*
         * Escape the complete route first.
         */
        $routeRegex = preg_quote($route, '~');

        /*
         * Replace escaped parameters with capture groups.
         */
        $routeRegex = preg_replace(
            '/\\\\\{([^}]+)\\\\}/',
            '([^/]+)',
            $routeRegex
        );

        if (!preg_match(
            '~^' . $routeRegex . '$~',
            $requestPath,
            $matches
        )) {
            return false;
        }

        /*
         * Remove the complete match.
         */
        array_shift($matches);

        /*
         * Build the parameter array.
         */
        $parameters = [];

        foreach ($parameterMatches[1] as $index => $parameter) {
            if (!isset($matches[$index])) {
                continue;
            }

            $value = $matches[$index];

            if (is_numeric($value)) {
                $value = (int) $value;
            }

            $parameters[$parameter] = $value;
        }

        return $parameters;
    }

    /**
     * Executes a matched route including all registered middleware.
     *
     * @param Route $route
     * @param array $parameters
     * @return void
     */
    private static function executeRoute(
        Route $route,
        array $parameters
    ): void {
        /*
         * Execute middleware.
         */
        foreach ($route->getMiddlewares() as $middleware) {
            $className = 'App\\Middleware\\' . $middleware['class'];
            $method = $middleware['method'];

            $class = new $className;

            $class->$method();
        }
        /*
         * Execute controller.
         */
        $controller = 'App\\Controllers\\' . $route->getController();
        $method = $route->getAction();

        $class = new $controller;

        if (empty($parameters)) {
            $class->$method();

            return;
        }

        $class->$method(...array_values($parameters));
    }

    /**
     * Returns a 404 response.
     *
     * @return never
     */
    private static function notFound(): never
    {
        http_response_code(404);

        echo '404 Not Found';

        exit();
    }
}