<?php

namespace App\Controllers;
use App\Middleware\PreventGuestMiddleware;
use Core\Bases\BaseController;


class UserController extends BaseController{

    public function __construct(){
        $this->registerMiddleware(new PreventGuestMiddleware(['profile']));
    }

    public function login()
    {
        return view("auth.login");
    }

    //this function run only when user logged (prevented with middleware)
    public function profile(){
        return view("profile");
    }
}