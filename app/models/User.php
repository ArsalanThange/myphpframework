<?php

namespace App\Models;

class User extends Model
{
    /**
     * Setting table value for User class.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Get the database column to be used for logging in.
     * Default returns username. (Can be email, mobile etc)
     *
     * @return string
     */
    public function getUsername()
    {
        return 'username';
    }

    /**
     * Declaring One-To-Many relationship between User and Message.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'relation' => 'hasMany',
            'class' => 'Message',
            'foreign_key' => 'user_id',
        ];
    }

    /**
     * Declaring One-To-Many relationship between User and Message.
     *
     * @return array
     */
    public function test()
    {
        return [
            'relation' => 'belongsToMany',
            'class' => 'Message',
            'table' => 'many_to_many',
            'primary_table_key' => 'user_id',
            'foreign_key' => 'message_id',
        ];
    }
}
