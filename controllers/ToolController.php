<?php

namespace controllers;

class ToolController {

    public function __construct()
    {
        if(config('mode') != 'dev')
        {
            die('非法访问');
        }
    }

    public function users(){

        $user = new \Models\User;
        $data = $user->getAll();

        echo json_encode([

            'status_code' => 200,
            'data' => $data
        ]); 
    }

    public function login(){

        $email = $_GET['email'];

        $_SESSION = [];

        $user = new \Models\User;
        $user->login($email,md5('123'));
    }
}