<?php

namespace Core;

use App\Exceptions\HttpException;

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

        $valid_routes = $this->isValidRoute($route);

        if(!count($valid_routes)) {
            throw new HttpException(404, 'Page not found.');
        }
    }

    /**
     * Validate the requested route against registered routes.
     *
     * @param string $route Requested route
     * @return array
     */
    public function isValidRoute($route)
    {
        $found = array_filter($this->routes, function ($elem) use ($route) {
            return $elem['name'] == $route;
        });

        return $found;
    }

    /**
     * Check if the HTTP Method of requested route is valid and registered.
     *
     * @param array $valid_routes Valid route endpoints for the requested route
     * @return array
     */
    public function isValidMethod($valid_routes)
    {
        $found = array_filter($valid_routes, function ($elem) {
            return $elem['type'] == $_SERVER['REQUEST_METHOD'];
        });

        return $found;
    }
}
