<?php

namespace App\controllers;

class IndexController
{
    public function indexPageAction(array $params, array $query) {
        session_start();
        if(!isset($_SESSION["username"]))
        {
            header("Location: /login");
        }
        else
        {
            header("Location: /user");
        }
        // /
    }
}
