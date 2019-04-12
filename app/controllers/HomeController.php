<?php

namespace App\Controllers;

use Core\Request;
use Core\Auth;
use App\Models\Message;
use App\Models\User;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $message = 'Welcome to my Framework';

        $this->view('index', $message)->render();
    }

    public function select(Request $request)
    {
        $message = new Message;

        $message = $message->select('*')
            //->where('is_deleted', '=', 0)
            ->with(['test']) //Fetch created message record along with each message
            ->orderBy('created_at', 'ASC')
            ->get();

        $this->view('select', $message)->render();
    }

    public function select2(Request $request)
    {
        $message = new Message;

        $message = $message->select('*')
            ->where('is_deleted', '=', 0)
            ->with(['user']) //Fetch created user record along with each message
            ->first();

        $this->view('select2', $message)->render();
    }

    public function insert(Request $request)
    {
        $message = new Message;

        $message->id = uniqid();
        $message->subject = 'Subject';
        $message->message = 'Test Message';
        $message->user_id = Auth::user()->id; //Get currently logged in user's ID
        $message->created_at = date('Y-m-d H:i:s');
        $message->modified_at = date('Y-m-d H:i:s');
        $message->save();

        $this->view('insert', $message)->render();
    }

    public function update(Request $request)
    {
        $message = new Message;
        $message = $message->select('*')->first();

        $old_message = clone $message;

        $message->subject = 'New Subject ' . rand();
        $message->message = 'new Test Message ' . rand();
        $message->modified_at = date('Y-m-d H:i:s');
        $message->update();

        $data['old_message'] = $old_message;
        $data['new_message'] = $message;

        $this->view('update', $data)->render();
    }

    public function requestValidation(Request $request)
    {
        $errors = $request->validate([
            'username' => [
                'required' => true,
                'min_length' => 5,
                'max_length' => 10,
            ],
        ]);

        if (count($errors)) {
            $this->addErrors($errors);
            $text = 'Errors found during validation. Please find them below';
        } else {
            $text = 'No Errors found';
        }

        $this->view('requestValidation', $text)->render();
    }
}
