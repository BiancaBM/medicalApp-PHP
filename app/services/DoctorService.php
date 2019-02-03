<?php

namespace App\Services;

use App\Models\User;

class DoctorService { 
    function getDoctors()
    {   
        $doctors = (new User)->getBy("isAdmin", false, true);
        return $doctors ? $doctors : [];  
    }
}
