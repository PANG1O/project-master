<?php
declare(strict_types=1);

namespace PHPCore\Core\Http;

/**
 * Represents a registered HTTP route.
 *
 * @author Julius Derigs <julius@derigs.top
 */
class Route
{
    ####################################################################################################################
    # --- PROPERTIES ------------------------------------------------------------------------------------------------- #
    ####################################################################################################################

    private string $method;

    private string $path;

    private string $controller;

    private string $action;

    private array $middlewares = [];

    ####################################################################################################################
    # --- CONSTRUCTOR ------------------------------------------------------------------------------------------------ #
    ####################################################################################################################

    /**
     * Creates a new route.
     *
     * @param string $method
     * @param string $path
     * @param string $controller
     * @param string $action
     */
    public function __construct(
        string $method,
        string $path,
        string $controller,
        string $action
    ) {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->controller = $controller;
        $this->action = $action;
    }

    ####################################################################################################################
    # --- PUBLIC METHODS --------------------------------------------------------------------------------------------- #
    ####################################################################################################################

    /**
     * Adds middleware to the route.
     *
     * @param string $middleware
     * @return static
     */
    public function middleware(string $middleware): static
    {
        if (!str_contains($middleware, '@')) {
            throw new \InvalidArgumentException(
                'Middleware must contain a class and method separated by "@".'
            );
        }

        [$class, $method] = explode('@', $middleware, 2);

        $this->middlewares[] = [
            'class' => $class,
            'method' => $method
        ];

        return $this;
    }

    /**
     * Returns the HTTP method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the route path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the controller.
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Returns the controller action.
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Returns all registered middleware.
     *
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}