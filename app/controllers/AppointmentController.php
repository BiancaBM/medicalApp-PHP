<?php

namespace App\controllers;

use Framework\Controller;
use App\Services\InterventionService;
use App\Services\AppointmentService;
use App\Services\PatientService;

class AppointmentController extends Controller
{
    private $appointmentInstance;
    private $interventionInstance;
    private $patientInstance;

    function __construct()
    {   
        parent::__construct();
        $this->appointmentInstance = new AppointmentService();
        $this->interventionInstance = new InterventionService();
        $this->patientInstance = new PatientService();

        if(!isset($_SESSION["username"])) header("Location: /");
    }


    public function appointmentAddPageAction() {
        if((isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || !$_SESSION['isActive']){
            header('Location: /');
        }

        $patients = $this->patientInstance->getPatients();
        $interventions = $this->interventionInstance->getInterventions();
        
        return $this->view('appointmentaddpage.html', ["interventions" => $interventions, "patients" => $patients]);
    }

    public function appointmentAddSaveAction(array $params) {
        if(!isset($_SESSION["idUser"]) || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || (isset($_SESSION["isActive"]) && !$_SESSION['isActive'])) {
            header('Location: /notfound');
        }
        
        $added = $this->appointmentInstance->addAppointment($params["selectedPatient"], $params["startDate"], $params["endDate"], $params["selectedInterventions"]);

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

    public function appointmentRemoveAction(array $params) {
        $this->appointmentInstance->removeAppointment($params["idAppointment"]);
        // /appointment/remove
    }
}
