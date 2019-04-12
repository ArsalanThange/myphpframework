<?php

namespace Core;

class Route
{
    /**
     * Holds all valid routes for this application.
     *
     * @var array
     */
    public $routes = [];

    /**
     * Construct GET request URL routes.
     *
     * @param string $route Route for the application
     * @param string $to    To which controller and method should this route go
     * @param string $auth  Access to the route ("auth" for logged in users, "guest" for logged out users, "both" for all users)
     * @return void
     */
    public function get($route, $to, $auth)
    {
        $to = explode('@', $to);
        $this->routes[] = [
            'name' => $route,
            'type' => 'GET',
            'controller' => $to[0],
            'method' => $to[1],
            'guard' => $auth,
        ];
    }

    /**
     * Construct POST request URL routes.
     *
     * @param string $route Route for the application
     * @param string $to    To which controller and method should this route go
     * @param string $auth  Access to the route ("auth" for logged in users, "guest" for logged out users, "both" for all users)
     * @return void
     */
    public function post($route, $to, $auth)
    {
        $to = explode('@', $to);
        $this->routes[] = [
            'name' => $route,
            'type' => 'POST',
            'controller' => $to[0],
            'method' => $to[1],
            'guard' => $auth,
        ];
    }
}
