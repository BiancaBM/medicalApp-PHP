<?php

namespace App\Services;

class LogoutService {

    function logout()
    {
        session_start();
        if ( isset($_COOKIE[session_name()] ) )
        setcookie( session_name(), "", time()-3600, "/" );
        $_SESSION = array();
        session_destroy();
        if(($_SERVER['REQUEST_URI'])!="/login")
        {
            header("Location: /login");
        }
    }
}
