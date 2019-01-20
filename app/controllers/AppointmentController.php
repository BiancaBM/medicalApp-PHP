<?php

namespace App\controllers;

use Framework\Controller;
use App\Services\InterventionService;
use App\Services\AppointmentService;

class AppointmentController extends Controller
{
    private $appointmentInstance;
    function __construct()
    {   
        parent::__construct();
        $this->appointmentInstance = new AppointmentService();
        if(!isset($_SESSION["username"])) header("Location: /");
    }


    public function appointmentAddPageAction() {
        if((isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) || !$_SESSION['isActive']){
            header('Location: /');
        }

        $interventionInstance = new InterventionService();
        $interventionInstance->getInterventions();
        
        $interventions = $_SESSION["interventions"];
        return $this->view('appointmentaddpage.html', ["interventions" => $interventions]);
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
