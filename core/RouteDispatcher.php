<?php

namespace Core;

class RouteDispatcher
{
    /**
     * Holds all valid routes for this application.
     *
     * @var array
     */
    public $routes = [];

    /**
     * Construct validation of incoming Request Route.
     *
     * @param string $routes Routes for the application
     * @return void
     */
    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->checkRoutes();
    }

    /**
     * Check the requested route against registered route.
     *
     * @return void
     */
    public function checkRoutes()
    {
        $route = explode('?', trim($_SERVER['REQUEST_URI']), 2)[0];
    }

    /**
     * Validate the requested route against registered routes.
     *
     * @param string $route Requested route
     * @return void
     */
    public function isValidRoute($route)
    {
        $found = array_filter($this->routes, function ($elem) use ($route) {
            return $elem['name'] == $route;
        });

        return $found;
    }
}
