<?php

namespace App\Controllers;

class HttpController extends Controller
{
    /**
     * Generate HTTP error response code and send to appropriate Error view file.
     * Dies after execution to prevent loading of requested view.
     *
     * @param string $error HTTP error codes such as 404, 403, 429 etc
     * @return void
     */
    public function httpError($error)
    {
        http_response_code($error);

        if (file_exists(__DIR__.'/../views/errors/' . $error . '.vw.php')) {
            $this->view('errors/' . $error)->render();
        } else {
            $this->view('errors/500')->render();
        }

        die();
    }
}
