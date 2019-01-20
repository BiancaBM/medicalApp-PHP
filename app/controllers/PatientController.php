<?php

namespace App\controllers;

use \App\Models\Patient;
use \App\Services\PatientService;
use Framework\Controller;

class PatientController extends Controller
{
    private $patientInstance;
    function __construct()
    {   
        parent::__construct();
        $this->patientInstance = new PatientService();
        if(!isset($_SESSION["username"])) header("Location: /");
    }
    
    public function patientEditPageAction($params) {
        if($params['idPatient'] == null) {
            header('Location: /');
        }

        $patient = (new Patient)->getBy('idPatient', $params['idPatient']);
        
        return $this->view('patienteditpage.html', ['patient' => $patient]);
        // /patient/edit
    }

    public function patientAddPageAction() {
        if((isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || !$_SESSION['isActive']){
            header('Location: /');
        }

        return $this->view('patientaddpage.html');
    }

    public function patientEditSaveAction(array $params) {
        $this->patientInstance->editPatient($params["idPatient"], $params["firstName"], $params["lastName"], $params["telephone"], $params["address"]);
        header('Location: /user');
    }

    public function patientRemoveAction(array $params) {
        $this->patientInstance->removePatient($params["idPatient"], $params["fullName"]);
        // /patient/remove
    }

    public function patientAddSaveAction(array $params) {
        if(!isset($_SESSION["idUser"]) || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || (isset($_SESSION["isActive"]) && !$_SESSION['isActive'])) {
            header('Location: /notfound');
        }

        $added = $this->patientInstance->addPatient($params["firstName"], $params["lastName"], $params["cnp"], $params["telephone"], $params["address"]);

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
