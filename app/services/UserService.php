<?php

namespace App\services;

use PDO;

class UserService { 

    private $pdo;

    function __CONSTRUCT(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function editProfile(string $firstName = null, string $lastName = null, string $telephone = null)
    {   
        $sql="UPDATE `users` SET `firstName` = (?),`lastName` = (?), `telephone` = (?) WHERE `idUser` = (?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$firstName,$lastName,$telephone,$_SESSION["idUser"]]);
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $_SESSION["firstName"]=$firstName;
            $_SESSION["lastName"]=$lastName;
            $_SESSION["telephone"]=$telephone;
        } 
    }

    function changeStatus(string $idUser, string $isActive){
        $sql="UPDATE `users` SET `isActive` = (?) WHERE `idUser` = (?)";
        $stmt = $this->pdo->prepare($sql);
        $status = $stmt->execute([$isActive,$idUser]);
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            header("Location: /admin");
        } 
    }
}
