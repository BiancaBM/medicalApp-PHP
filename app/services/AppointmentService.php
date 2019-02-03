<?php

namespace App\Services;

use App\Services\MessageService;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\AppointmentIntervention;
use App\Models\Intervention;

class AppointmentService { 

    private $messageInstance;

    function __construct() {
        $this->messageInstance = new MessageService();
    }

    function getAppointments()
    {   
        $result = (new Appointment)->getBy("idUser", $_SESSION["idUser"], true);

        $appointments = [];

        if(count($result) > 0){
            foreach($result as $appointment){
                $this->fillPatientName($appointment);
                $this->fillInterventionsAndTotalPrice($appointment);
                array_push($appointments, $appointment);
            }
        }
        
        return $appointments;
    }

    function fillPatientName(&$appointment) {
        $patient = (new Patient)->getBy("idPatient", $appointment["idPatient"]);

        $appointment["patientFullName"] = $patient["lastName"].' '.$patient["firstName"];
        $appointment["patientCnp"] = $patient["CNP"];
    }

    function fillInterventionsAndTotalPrice(&$appointment) {
        $result = (new AppointmentIntervention)->getBy("idAppointment", $appointment["idAppointment"], true);
            
        $interventions = [];
        $totalPrice = 0;

        if(count($result) > 0){
            foreach($result as $appInterv) {
                $intervention = (new Intervention)->getBy("idIntervention", $appInterv["idIntervention"]);
                $totalPrice+= $intervention["price"];
                array_push($interventions, $intervention);
            }
        }

        $appointment["interventions"] = $interventions;
        $appointment["totalPrice"] = $totalPrice;
    }

    function removeAppointment(string $idAppointment = null){
        if($idAppointment == null){
            header('Location: /notfound');
            return false;
        }
        
        $this->removeLinkedInterventions($idAppointment);
        
        (new Appointment)->deleteBy('idAppointment', $idAppointment);

        $this->messageInstance->setGeneralMsgInSession("Selected appointment was successfully removed!", false);

        header('Location: /');
    }

    function removeLinkedInterventions(string $idAppointment){
        (new AppointmentIntervention)->deleteBy('idAppointment', $idAppointment);
    }

    function addAppointment(string $selectedPatientId = null, string $startDate = null, string $endDate = null, array $selectedInterventions = null)
    {   
        if($selectedPatientId == null || $startDate == null || $endDate == null) {
            header('Location: /notfound');
        }

        if(!(new Appointment)->isIntervalAvailable($startDate, $endDate)){
            $this->messageInstance->setGeneralMsgInSession("Dates conflict! Please change it!", true);
            return FALSE;
        }     
        
        $itemToInsert = ['startDate' => $startDate, 'endDate' => $endDate, 'idPatient' => $selectedPatientId, 'idUser' => $_SESSION['idUser']];
        $insertedAppointmentId = (new Appointment)->insert($itemToInsert);

        if($insertedAppointmentId != null && $selectedInterventions != null && count($selectedInterventions) > 0){
            $this->createIntervetionsAppointmentsLinks($insertedAppointmentId, $selectedInterventions);
        }
        
        $this->messageInstance->setGeneralMsgInSession("Appointment successfully added!", false);
        return TRUE;
    }

    function createIntervetionsAppointmentsLinks(string $idAppointment, array $interventionIds){
        foreach ($interventionIds as &$idIntervention) {
            $itemToInsert = ['idAppointment' => $idAppointment, 'idIntervention' => $idIntervention];
            $insertedId = (new AppointmentIntervention)->insert($itemToInsert);
        }
    }
}
