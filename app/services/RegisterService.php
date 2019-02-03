<?php

namespace App\Services;

use \App\Models\User;
use \App\Services\AuthService;

class RegisterService
{
    private $username;
    private $password;
    private $cnp;

    function __construct(string $username, string $password, string $cnp)
    {
        $this->username = $username;
        $this->password = $password;
        $this->cnp = $cnp;
    }

    function checkUsernameOrCnpExists()
    {
        $usernameExist = (new User)->getBy('username', $this->username);
        $cnpExist = (new User)->getBy('cnp', $this->cnp);

        return $usernameExist || $cnpExist;
    }

    function registerUserInDb()
    {
        if ($this->checkUsernameOrCnpExists() == false)
        {
            $options = [
                'cost' => 12,
            ];

            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT, $options); // always 60 characters

            $itemToInsert = ['password' => $hashedPassword, 'username' => $this->username, 'cnp' => $this->cnp];
            (new User)->insert($itemToInsert);

            return TRUE;
        }
        return FALSE;
    }

    function callAutoLogin()
    {
        $authenticateInstance = new AuthService($this->username, $this->password);
        $authenticationResult = $authenticateInstance->authenticateUser();
        $authenticateInstance->redirectAuthenticationForm($authenticationResult);
    }
}
