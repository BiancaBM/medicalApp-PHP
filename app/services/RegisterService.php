<?php

namespace App\services;

use PDO;
use \App\services\AuthService;

class RegisterService
{
    private $username;
    private $password;
    private $cnp;
    private $pdo;

    function __CONSTRUCT(string $username, string $password, string $cnp, PDO $pdo)
    {
        $this->username = $username;
        $this->password = $password;
        $this->cnp = $cnp;
        $this->pdo = $pdo;
    }

    function checkUsernameOrCnpExists()
    {
        $sql = "SELECT * FROM users WHERE username = (?) or cnp = (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->username, $this->cnp]);
        $result = $stmt->fetch();
        if ($result) {
            return true;
        }

        return false;
    }

    function registerUserInDb()
    {
        if ($this->checkUsernameOrCnpExists() == false)
        {
            $sql = "INSERT INTO `users` (password,username, cnp) VALUES(?,?,?)";
            
            $options = [
                'cost' => 12,
            ];

            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT, $options); // always 60 characters
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$hashedPassword, $this->username, $this->cnp]);
            return TRUE;
        }
        return FALSE;
    }

    function callAutoLogin()
    {
        $authenticateInstance = new AuthService($this->username, $this->password, $this->pdo);
        $authenticationResult = $authenticateInstance->authenticateUser();
        $authenticateInstance->redirectAuthenticationForm($authenticationResult);
    }
}
