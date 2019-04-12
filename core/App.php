<?php

namespace Core;

use Core\Request;
use App\Controllers\HttpController;

class App
{
    /**
     * Default controller for routing.
     *
     * @var string
     */
    protected $controller = 'App\Controllers\HomeController';

    /**
     * Default method for routing.
     *
     * @var string
     */
    protected $method = 'index';

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

        $this->parseURL();
        $controller = new $this->controller;
        $method = $this->method;

        $request = new Request($_REQUEST);

        $controller->$method($request);
    }

    /**
     * Parses the incoming URL and segregates it into Controllers, methods and parameters.
     * Takes care of URL access to guests and authenticated users.
     * Redirects to appropriate http error in case of invalid or unauthorized access.
     *
     * @return void
     */
    protected function parseURL()
    {
        include '../routes/routes.php';

        $request = explode('?', trim($_SERVER['REQUEST_URI']), 2)[0];
        $found = 0;

        foreach ($route->routes as $key => $value) {

            if ($value['name'] == $request && $_SERVER['REQUEST_METHOD'] == $value['type']) {

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->validateCSRF();
                }

                $found = 1;

                if ($value['guard'] == 'auth' && !Auth::check()) {
                    header('Location: /login');
                    break;
                }

                if ($value['guard'] == 'guest' && isset($_SESSION['is_logged_in'])) {
                    header('Location: /');
                    break;
                }

                $this->controller = 'App\Controllers\\'.$value['controller'];
                $this->method = $value['method'];
                break;

            }

        }

        if (!$found) {
            $this->throwHttpError(404);
        }
    }

    /**
     * Validates incoming POST requests for CSRF Token.
     * Throws and redirects user to unauthorized page if the token is invalid.
     *
     * @return void
     */
    public function validateCSRF()
    {
        if ($_REQUEST['token'] != $_SESSION['token']) {
            $this->throwHttpError(403);
        }
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
