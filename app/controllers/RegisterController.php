<?php

namespace App\controllers;

use Framework\Controller;
use App\Services\RegisterService;

class RegisterController extends Controller
{

    function __construct()
    {   
        parent::__construct();
        if(isset($_SESSION["username"])) header("Location: /");
    }
    
    public function registerPageAction() {
        return $this->view('signuppage.html');
        // /register
    }

    public function registerNowAction(array $params) {
        $registerInstance = new RegisterService($params["username"],$params["password"],$params["cnp"]);
        if($registerInstance->registerUserInDb()) {
            $registerInstance->callAutoLogin();
        }
        else{
            session_start();
            $_SESSION["generalMsg"] = "This username/CNP is already used!"."___TIMESTAMP___".time();
            $_SESSION["isErrorMessage"] = true;
            header("Location: /register");
        }
        // /register
    }
}
