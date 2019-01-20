<?php

namespace App\Services;

use App\Models\User;

class ResetPasswordService { 

    function __CONSTRUCT()
    {
    }

    function resetPassword(string $password = null, string $confirm_password = null)
    {
        if($password === $confirm_password && $password != null)
        {
            $password=password_hash($password,PASSWORD_DEFAULT);
            $data = ['password' => $password];
            $where = ['idUser' => $_SESSION["idUser"]];
            (new User)->update($where, $data);
            
            $_SESSION["generalMsg"] = "Password has been reset successfully!"."___TIMESTAMP___".time();
            $_SESSION["isErrorMessage"] = false;
            return TRUE;
        }

        return FALSE;
    }
}
