<?php

namespace Core;

use App\Controllers\HttpController;
use Core\RouteDispatcher;

class App
{
    /**
     * Get controllers and methods from URL and send them for execution.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setAppDefaults();

        if (getConfig('enable_request_throttle')) {
            $this->throttleRequests();
        }

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
     * Throttles incoming requests to prevent DOS attacks.
     *
     * @return void
     */
    protected function throttleRequests()
    {
        if (isset($_SESSION['request_throttle_count'])) {
            $_SESSION['request_throttle_count']++;
        } else {
            $_SESSION['request_throttle_count'] = 1;
        }

        if (isset($_SESSION['request_throttle_time'])) {

            $current_time = new \DateTime();
            $current_time = $current_time->modify('-1 second')->format('Y-m-d H:i:s');

            if ($_SESSION['request_throttle_time'] < $current_time) {

                //Update throttle time and reset current request count
                $_SESSION['request_throttle_time'] = date('Y-m-d H:i:s');
                $_SESSION['request_throttle_count'] = 0;

            } else {

                if ($_SESSION['request_throttle_count'] > getConfig('request_throttle_count')) {
                    $this->throwHttpError(429);
                }

            }

        } else {
            $_SESSION['request_throttle_time'] = date('Y-m-d H:i:s');
        }
    }

    /**
     * Throws and redirects user to http error page.
     *
     * @param int|string $error_code    HTTP error code
     * @return void
     */
    public function throwHttpError($error_code)
    {
        $http = new HttpController;
        $http->httpError($error_code);
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
