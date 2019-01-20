<?php

namespace App\Services;

use App\Models\User;

class AuthService { 
    private $username;
    private $password;

    function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
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
        $result = (new User)->getBy("username", $this->username);

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
            $_SESSION["generalMsg"] = "The username or password is wrong!"."___TIMESTAMP___".time();
            $_SESSION["isErrorMessage"] = true;
            header("Location: /login");
        }
    }

    
}
