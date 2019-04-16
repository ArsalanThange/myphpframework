<?php

namespace App\Middlewares;

class Middleware
{
    /**
     * Holds registered middlewares for this application.
     *
     * @var array
     */
    public $middlewares = [];

    /**
     * Construct middleware registration.
     *
     * @return void
     */
    public function __construct()
    {
        $this->registerMiddlewares();
    }

    /**
     * Register all midldewares for the application.
     *
     * @return void
     */
    protected function registerMiddlewares()
    {
        $this->middlewares = [
            'auth' => \App\Middlewares\Authenticate::class,
        ];
    }
}
