<?php

namespace Core;

use Core\Auth;

class View
{
    /**
     * Template file containing HTML for the view.
     *
     * @var string
     */
    protected $view_file;

    /**
     * Data which is to be passed to the view.
     *
     * @var mixed
     */
    protected $view_data;

    /**
     * Errors which are generator for this view.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Prepare view, data and errors for rendering the view.
     *
     * @param string $view_file File path to load the view
     * @param mixed $view_data  Data to be sent to the view for display
     * @return void
     */
    public function __construct($view_file, $view_data)
    {
        $this->view_file = $view_file;
        $this->view_data = $view_data;

        $this->setErrors();
    }

    /**
     * Render the requested view.
     *
     * @return void
     */
    public function render()
    {
        include __DIR__ . '/../app/views/layouts/header.vw.php';
        include __DIR__ . '/../app/views/' . $this->view_file . '.vw.php';
        include __DIR__ . '/../app/views/layouts/footer.vw.php';

        //After rendering the view and showing the errors, unset the current set of errors.
        $this->unsetErrors();
    }

    /**
     * Set validation/session errors.
     *
     * @return void
     */
    public function setErrors()
    {
        if (isset($_SESSION['errors'])) {
            $this->errors = $_SESSION['errors'];
        }
    }

    /**
     * Unset validation/session errors.
     *
     * @return void
     */
    public function unsetErrors()
    {
        unset($_SESSION['errors']);
    }

}
