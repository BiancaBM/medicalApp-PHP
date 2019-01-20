<?php

namespace App\controllers;

use Framework\Controller;
use App\Services\AuthService;
use App\Services\LogoutService;
use App\Services\RegisterService;

class LoginController extends Controller
{
    function __construct()
    {
        parent::__construct();
        if(isset($_SESSION["username"])) header("Location: /");
    }

    public function loginPageAction() {      
        return $this->view('loginpage.html');
        // /login
    }

    public function loginAuthAction(array $params) {
        $authenticateInstance = new AuthService($params["username"],$params["password"]);
        $authentificationResult = $authenticateInstance->authenticateUser();
        $authenticateInstance->redirectAuthenticationForm($authentificationResult);
        // /login/auth
    }

    public function logoutAction() {
        $logOutInstance = new LogoutService();
        $logOutInstance->logout();
        // /logout
    }
}
