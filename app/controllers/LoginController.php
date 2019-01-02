<?php

namespace App\controllers;

use \App\services\AuthService;
use \App\services\LogoutService;
use \App\services\RegisterService;
use \App\services\DatabaseConnection;

class LoginController
{
    private $pdo;

    function __CONSTRUCT()
    {
        session_start();
        if(isset($_SESSION["username"])) header("Location: /");
        $databaseConnectionInstance = new DatabaseConnection(); 
        $this->pdo = $databaseConnectionInstance->CreateDatabaseConnection();
    }
    public function loginPageAction(array $params, array $query) {
        include(__DIR__ . '\..\views\loginpage.phtml');
        // /login
    }

    public function loginAuthAction(array $params, array $query) {
        $authenticateInstance = new AuthService($params["username"],$params["password"],$this->pdo);
        $authentificationResult = $authenticateInstance->authenticateUser();
        $authenticateInstance->redirectAuthenticationForm($authentificationResult);
        // /login/auth
    }

    public function logoutAction(array $params, array $query) {
        $logOutInstance = new LogoutService();
        $logOutInstance->logout();
        // /logout
    }
}
