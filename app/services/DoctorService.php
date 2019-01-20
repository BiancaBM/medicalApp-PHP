<?php

namespace App\Services;

use App\Models\User;

class DoctorService { 

    function getDoctors()
    {   
        $doctors = (new User)->getBy("isAdmin", false, true);
        $_SESSION["doctors"]=$doctors ? $doctors : [];  
    }
}
