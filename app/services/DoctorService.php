<?php

namespace App\services;

use PDO;

class DoctorService { 

    private $pdo;

    function __CONSTRUCT(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    function getDoctors()
    {   
         //session_start();
         $sql="SELECT * FROM users WHERE isAdmin='0'";
         $stmt = $this->pdo->prepare($sql);
         $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        else
        {
            $doctors= [];
            while($row = $stmt->fetch()) {
                array_push($doctors, $row);
            }
            
            $_SESSION["doctors"]=$doctors;
        } 
    }
}
