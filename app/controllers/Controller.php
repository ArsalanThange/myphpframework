<?php

namespace App\Controllers;

use Core\View;

class Controller
{
    /**
     * View instance which is to be generated for incoming request
     *
     * @var View
     */
    protected $view;

    /**
     * Generate view instance.
     *
     * @param string $view_file File path to load the view
     * @param mixed $view_data  Data to be sent to the view for display
     * @return View
     */
    public function view($view_file, $view_data = [])
    {
        $this->view = new View($view_file, $view_data);

        return $this->view;
    }

    /**
     * Add errors to session which are to be displayed in the incoming view.
     *
     * @param mixed array|string $errors    Errors which are to be displayed to the user
     * @return void
     */
    public function addErrors($errors)
    {
        if (is_array($errors)) {
            foreach ($errors as $key => $error) {
                $_SESSION['errors'][] = $error;
            }
        } else {
            $_SESSION['errors'][] = $errors;
        }
    }
}
