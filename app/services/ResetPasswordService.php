<?php

namespace App\services;

use PDO;

class ResetPasswordService { 

    private $pdo;

    function __CONSTRUCT(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function resetPassword(string $password = null, string $confirm_password = null)
    {
        if($password === $confirm_password && $password != null)
        {
            $sql="UPDATE `users` SET `password` = (?) WHERE `idUser` = (?)";
            $stmt = $this->pdo->prepare($sql);
            $password=password_hash($password,PASSWORD_DEFAULT);
            $status = $stmt->execute([$password,$_SESSION["idUser"]]);
            if ($status === false) {
                trigger_error($stmt->error, E_USER_ERROR);
            } 

            $_SESSION["generalMsg"] = "Password has been reset successfully!";
            return TRUE;
        }

        return FALSE;
    }
}
