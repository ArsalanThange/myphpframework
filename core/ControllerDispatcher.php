<?php

namespace Core;

use Core\Request;

class ControllerDispatcher
{
    /**
     * Namespace for controllers.
     *
     * @var string
     */
    protected $namespace = 'App\Controllers\\';

    /**
     * Default controller for routing.
     *
     * @var string
     */
    protected $controller = 'HomeController';

    /**
     * Default method for routing.
     *
     * @var string
     */
    protected $method = 'index';

    /**
     * Construct Controller and method for dispatch.
     *
     * @param string $controller    Controller for the requested route
     * @param string $method        Method for the requested route
     * @return void
     */
    public function __construct($controller, $method)
    {
        $this->controller = $controller;
        $this->method = $method;

        $this->dispatch();
    }

    /**
     * Dispatch the controller and method for the requested route.
     *
     * @return void
     */
    public function dispatch()
    {
        $controller = $this->namespace . $this->controller;
        $c = new $controller;
        $method = $this->method;

        $request = new Request($_REQUEST);

        $c->$method($request);
    }
}
