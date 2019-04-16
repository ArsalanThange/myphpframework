<?php

namespace App\Middlewares;

use Core\Auth;

class Authenticate extends Middleware
{
    /**
     * Construct authentication check.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authenticateCheck();
    }

    /**
     * Check if the user is logged in, if not redirect to login page.
     *
     * @return void
     */
    public function authenticateCheck()
    {
        if (!Auth::check()) {
            redirect('/login');
        }
    }
}
