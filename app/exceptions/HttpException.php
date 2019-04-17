<?php

namespace App\Exceptions;

use App\Controllers\Controller;

class HttpException extends \Exception
{
    /**
     * Generate HTTP error response code and send to appropriate Error view file.
     * Dies after execution to prevent loading of requested view.
     *
     * @param string $error HTTP error codes such as 404, 403, 429 etc
     * @param string $message Message which is to be displayed when throwing error
     * @return void
     * 
     * @throws \Exception
     */
    public function __construct($error, $message = "Something went wrong.")
    {
        http_response_code($error);

        if (file_exists(__DIR__ . '/../views/errors/' . $error . '.vw.php')) {
            $controller = new Controller;
            $controller->view('errors/' . $error)->render();
        } else {
            throw new \Exception($message);
        }
        die();
    }
}
