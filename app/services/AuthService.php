<?php

namespace App\services;

use PDO;

class AuthService { 
    private $username;
    private $password;
    private $pdo;

    function __CONSTRUCT(string $username, string $password, PDO $pdo)
    {
        $this->username = $username;
        $this->password = $password;
        $this->pdo = $pdo;
    }

    function initializeSession(array $result)
    {
        $_SESSION["username"]=$this->username;
        $_SESSION["idUser"] = $result["idUser"];
        $_SESSION["lastName"]=$result["lastName"];
        $_SESSION["firstName"]=$result["firstName"];
        $_SESSION["cnp"]=$result["CNP"];
        $_SESSION["telephone"]=$result["telephone"];
        $_SESSION["isAdmin"]=$result["isAdmin"];
        $_SESSION["isActive"]=$result["isActive"];
    }

    function authenticateUser()
    {
        $sql="SELECT * FROM users WHERE username = (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->username]);
        $result = $stmt->fetch();

        if($result)
        {
           if(password_verify($this->password, $result['password']))
            {
                $this->initializeSession($result);
                return TRUE;
            }
        }
        return FALSE;
    }

    function redirectAuthenticationForm(bool $authenticateResult)
    {
        if($authenticateResult)
        {
            header("Location: /");
        }
        else
        {
            $_SESSION["generalMsg"] = "The username or password is wrong!";
            header("Location: /login");
        }
    }

    
}
