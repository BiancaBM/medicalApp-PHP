<?php

namespace App\Services;

use App\Models\User;

class UserService { 

    function editProfile(string $firstName = null, string $lastName = null, string $telephone = null)
    {   
        $data = ['firstName' => $firstName, 'lastName' => $lastName, 'telephone' => $telephone ];
        $where = ['idUser' => $_SESSION["idUser"]];
        (new User)->update($where, $data);
        
        $_SESSION["firstName"]=$firstName;
        $_SESSION["lastName"]=$lastName;
        $_SESSION["telephone"]=$telephone;
    }

    function changeStatus(string $idUser, string $isActive){
        $data = ['isActive' => $isActive ];
        $where = ['idUser' => $idUser];

        (new User)->update($where, $data);
        
        header("Location: /admin");
    }
}
