<?php

namespace App\controllers;

use \App\services\PatientService;
use \App\services\DatabaseConnection;

class PatientController
{
    private $pdo;

    function __CONSTRUCT()
    {   
        session_start();
        if(!isset($_SESSION["username"])) header("Location: /");
        $databaseConnectionInstance = new DatabaseConnection(); 
        $this->pdo = $databaseConnectionInstance->CreateDatabaseConnection();
    }
    
    public function patientEditPageAction(array $params, array $query) {
        $patientInstance = new PatientService($this->pdo);
        if(isset($_SESSION["selectedPatient"])) {
            header('Location: /');
        }

        $patientInstance->setSelectedPatient($params["idPatient"], $params["firstName"], $params["lastName"], $params["cnp"], $params["telephone"], $params["address"]);
        include(__DIR__ . "\..\htmls\patienteditpage.phtml");
        // /patient/edit
    }

    public function patientAddPageAction(array $params, array $query) {
        if((isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || !$_SESSION['isActive']){
            header('Location: /');
        }
        include(__DIR__ . "\..\htmls\patientaddpage.phtml");
    }

    public function patientEditSaveAction(array $params, array $query) {
        $patientInstance = new PatientService($this->pdo);
        $patientInstance->editPatient($_SESSION["selectedPatient"]["idPatient"], $params["firstName"], $params["lastName"], $params["telephone"], $params["address"]);
        header('Location: /user');
    }

    public function patientRemoveAction(array $params, array $query) {
        $patientInstance = new PatientService($this->pdo);
        $patientInstance->removePatient($params["idPatient"], $params["fullName"]);
        // /patient/remove
    }

    public function patientAddSaveAction(array $params, array $query) {
        $patientInstance = new PatientService($this->pdo);
        if(!isset($_SESSION["idUser"]) || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || (isset($_SESSION["isActive"]) && !$_SESSION['isActive'])) {
            header('Location: /notfound');
        }

        $added = $patientInstance->addPatient($params["firstName"], $params["lastName"], $params["cnp"], $params["telephone"], $params["address"]);

        if($added){
            unset($_SESSION['addedPatient']);
            header('Location: /user');
        } else {
            $_SESSION["addedPatient"]["firstName"] = $params["firstName"];
            $_SESSION["addedPatient"]["lastName"] = $params["lastName"];
            $_SESSION["addedPatient"]["cnp"] = $params["cnp"];
            $_SESSION["addedPatient"]["telephone"] = $params["telephone"];
            $_SESSION["addedPatient"]["address"] = $params["address"];
            header('Location: /patient/add');
        }
        // /patient/add/save
    }
}
