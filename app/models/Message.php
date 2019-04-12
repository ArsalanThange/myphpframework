<?php

namespace App\Models;

class Message extends Model
{
    /**
     * Setting table value for Message class.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * Declaring Many-To-One relationship between Message and User.
     *
     * @return array
     */
    public function user()
    {
        return [
            'relation' => 'belongsTo',
            'class' => 'User',
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
            'class' => 'User',
            'table' => 'many_to_many',
            'primary_table_key' => 'message_id',
            'foreign_key' => 'user_id'
        ];
    }
}
