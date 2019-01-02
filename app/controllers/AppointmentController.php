<?php

namespace App\controllers;

use \App\services\InterventionService;
use \App\services\AppointmentService;
use \App\services\DatabaseConnection;

class AppointmentController
{
    private $pdo;

    function __CONSTRUCT()
    {   
        session_start();
        if(!isset($_SESSION["username"])) header("Location: /");
        $databaseConnectionInstance = new DatabaseConnection(); 
        $this->pdo = $databaseConnectionInstance->CreateDatabaseConnection();
    }


    public function appointmentAddPageAction(array $params, array $query) {
        if((isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || !$_SESSION['isActive']){
            header('Location: /');
        }

        $interventionInstance = new InterventionService($this->pdo);
        $interventionInstance->getInterventions();

        include(__DIR__ . "\..\htmls\appointmentaddpage.phtml");
    }

    public function appointmentAddSaveAction(array $params, array $query) {
        $appointmentInstance = new AppointmentService($this->pdo);
        if(!isset($_SESSION["idUser"]) || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || (isset($_SESSION["isActive"]) && !$_SESSION['isActive'])) {
            header('Location: /notfound');
        }
        
        $added = $appointmentInstance->addAppointment($params["selectedPatient"], $params["startDate"], $params["endDate"], $params["selectedInterventions"]);

        if($added){
            unset($_SESSION['addedAppointment']);
            header('Location: /user');
        } else {
            $_SESSION["addedAppointment"]["selectedPatient"] = $params["selectedPatient"];
            $_SESSION["addedAppointment"]["selectedInterventions"] = $params["selectedInterventions"];
            header('Location: /appointment/add');
        }
        // /appointment/add/save
    }

    public function appointmentRemoveAction(array $params, array $query) {
        $appointmentInstance = new AppointmentService($this->pdo);
        $appointmentInstance->removeAppointment($params["idAppointment"]);
        // /appointment/remove
    }
}
