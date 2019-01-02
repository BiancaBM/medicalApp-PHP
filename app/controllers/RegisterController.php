<?php

namespace App\controllers;

use \App\services\RegisterService;
use \App\services\DatabaseConnection;

class RegisterController
{
    private $pdo;

    function __CONSTRUCT()
    {   
        session_start();
        if(isset($_SESSION["username"])) header("Location: /");
        $databaseConnectionInstance = new DatabaseConnection(); 
        $this->pdo = $databaseConnectionInstance->CreateDatabaseConnection();
    }
    
    public function registerPageAction(array $params, array $query) {
        include(__DIR__ . "\..\htmls\signuppage.phtml");
        // /register
    }

    public function registerNowAction(array $params, array $query) {
        $registerInstance = new RegisterService($params["username"],$params["password"],$params["cnp"],$this->pdo);
        if($registerInstance->registerUserInDb()) {
            $registerInstance->callAutoLogin();
        }
        else{
            session_start();
            $_SESSION["generalMsg"] = "This username/CNP is already used!";
            header("Location: /register");
        }
        // /register
    }
}
