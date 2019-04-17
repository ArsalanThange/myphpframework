<?php

namespace Core;

use Core\RouteDispatcher;

class App
{
    /**
     * Bootstrap the application.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setAppDefaults();
        $this->dispatchRoute();
    }

    /**
     * Send application to RouteDispatcher for execution.
     *
     * @return void
     */
    protected function dispatchRoute()
    {
        include '../routes/routes.php';
        $route_dispatcher = new RouteDispatcher($route->routes);
    }

    /**
     * Sets default settings of the application such as Timezone.
     *
     * @return void
     */
    protected function setAppDefaults()
    {
        date_default_timezone_set(getConfig('timezone'));
    }
}
