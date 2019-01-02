<?php

namespace App\controllers;

use \App\services\InterventionService;
use \App\services\DatabaseConnection;

class InterventionController
{
    private $pdo;

    function __CONSTRUCT()
    {   
        session_start();
        if(!isset($_SESSION["username"])) header("Location: /");
        $databaseConnectionInstance = new DatabaseConnection(); 
        $this->pdo = $databaseConnectionInstance->CreateDatabaseConnection();
    }

    public function interventionAddPageAction(array $params, array $query) {
        if((isset($_SESSION['isAdmin']) && !$_SESSION['isAdmin'])){
            header('Location: /');
        }

        include(__DIR__ . '\..\views\interventionaddpage.phtml');
    }

    public function interventionAddSaveAction(array $params, array $query) {
        $interventionInstance = new InterventionService($this->pdo);
        if(!isset($_SESSION["idUser"]) || (isset($_SESSION['isAdmin']) && !$_SESSION['isAdmin'])) {
            header('Location: /notfound');
        }

        $added = $interventionInstance->addIntervention($params["name"], $params["price"]);
        // /intervention/add/save
    }

    public function interventionRemoveAction(array $params, array $query) {
        $interventionInstance = new InterventionService($this->pdo);
        $interventionInstance->removeIntervention($params["idIntervention"]);
        // /intervention/remove
    }
}
