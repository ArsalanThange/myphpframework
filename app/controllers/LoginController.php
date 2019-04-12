<?php

namespace App\Controllers;

use Core\Auth;
use Core\Request;

class LoginController extends Controller
{
    public function showlogin(Request $request)
    {
        $view = $this->view('login')->render();
    }

    public function login(Request $request)
    {
        $errors = $request->validate([
            'username' => [
                'required' => true,
            ],
        ]);

        if (count($errors)) {
            $this->addErrors($errors);
            header('Location: /login');
        } elseif (Auth::attempt($request->username, $request->password)) {
            header('Location: /');
        } else {
            $this->addErrors('Invalid Credentials');
            header('Location: /login');
        }
    }

    public function logout($request)
    {
        if (Auth::destroy()) {
            header('Location: /login');
        }
    }

}
