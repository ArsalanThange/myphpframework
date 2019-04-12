<?php

namespace App\Models;

use Core\QueryBuilder;

class Model
{
    /**
     * Current table for model instance.
     *
     * @var string
     */
    protected $table;

    /**
     * Attributes for the current model instance.
     * These correspond to the database columns usually but not always.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Relationship records that will be fetched for this instance.
     *
     * @var array
     */
    public $relations = [];

    /**
     * Call methods from QueryBuilder class using the Model's instance.
     *
     * @param string $method
     * @param mixed $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists(__CLASS__, $method)) {
            return $this->$method(...$parameters);
        } else {
            return $this->newQuery()->$method(...$parameters);
        }
    }

    /**
     * Make new instance of QueryBuilder for execution of Database queries.
     *
     * @return QueryBuilder
     */
    public function newQuery()
    {
        $qb = new QueryBuilder;
        $qb->model = $this;

        return $qb;
    }
    /**
     * Dynamically access the user's attributes.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : $this->$key;
    }

    /**
     * Dynamically set an attribute on the user.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}
