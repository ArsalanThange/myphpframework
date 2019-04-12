<?php

namespace Core;

use App\Models\User;

class Auth
{
    /**
     * Check whether the user is logged in or not.
     *
     * @return boolean
     */
    public static function check()
    {
        return isset($_SESSION['is_logged_in']) ? true : false;
    }

    /**
     * Get currently logged in user.
     *
     * @return mixed User|boolean
     */
    public static function user()
    {
        return isset($_SESSION['user']) ? $_SESSION['user'] : false;
    }

    /**
     * Attempt to log in user.
     * If logged in successfully, set the user's details in session.
     *
     * @param  string   $username   Username of the user trying to log in
     * @param  string   $password   Password of the user trying to log in
     * @return boolean
     */
    public static function attempt($username, $password)
    {
        $user = new User;
        $user = $user->select(['username', 'password', 'id'])
            ->where('username', '=', $username)
            ->first();

        if (!$user) {
            return false;
        } else {

            if (password_verify($password, $user->password)) {

                $_SESSION['is_logged_in'] = true;
                unset($user->password);
                $_SESSION['user'] = $user;

                return true;

            } else {
                return false;
            }
        }
    }

    /**
     * Destroy the current session.
     *
     * @return boolean
     */
    public static function destroy()
    {
        session_destroy();
        return true;
    }
}