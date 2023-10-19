<?php

namespace App\Controllers;


class UserController{
    public function login()
    {
        return view("auth.login");
    }
}