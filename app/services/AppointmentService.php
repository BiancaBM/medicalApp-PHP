<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\AppointmentIntervention;
use App\Models\Intervention;

class AppointmentService { 

    function __CONSTRUCT()
    {
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
        
        $_SESSION["appointments"]=$appointments;
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

        $_SESSION["generalMsg"] = "Selected appointment was successfully removed!"."___TIMESTAMP___".time();
        $_SESSION["isErrorMessage"] = false;
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

        if(!$this->isIntervalAvailable($startDate, $endDate)){
            $_SESSION["generalMsg"] = "Dates conflict! Please change it!"."___TIMESTAMP___".time();
            $_SESSION["isErrorMessage"] = true;
            return FALSE;
        }     
        else 
        {
            $itemToInsert = ['startDate' => $startDate, 'endDate' => $endDate, 'idPatient' => $selectedPatientId, 'idUser' => $_SESSION['idUser']];
            $insertedAppointmentId = (new Appointment)->insert($itemToInsert);

            if($insertedAppointmentId != null && $selectedInterventions != null && count($selectedInterventions) > 0){
                $this->createIntervetionsAppointmentsLinks($insertedAppointmentId, $selectedInterventions);
            }

            $_SESSION["generalMsg"] = "Appointment successfully added!"."___TIMESTAMP___".time();
            $_SESSION["isErrorMessage"] = false;
        } 
        
        return TRUE;
    }

    function isIntervalAvailable(string $startDate, string $endDate) {
        $sql = "SELECT * FROM `appointments` WHERE (startDate >= (?) AND startDate <= (?)) OR (endDate >= (?) AND endDate <= (?))"; // check to not include other intervals inside of it
        $sql.= "OR ((?) >= startDate && (?) <= endDate) OR ((?) >= startDate && (?) <= endDate)"; // check that selected interval is not inside of another intervals
        $params = [
            $startDate,$endDate,$startDate,$endDate,
            $startDate, $startDate, $endDate, $endDate
        ];
        
        $result = (new Appointment)->runScript($sql, $params);

        if(count($result) > 0) {
            return FALSE;
        }

        return TRUE;
    }

    function createIntervetionsAppointmentsLinks(string $idAppointment, array $interventionIds){
        foreach ($interventionIds as &$idIntervention) {
            $itemToInsert = ['idAppointment' => $idAppointment, 'idIntervention' => $idIntervention];
            $insertedId = (new AppointmentIntervention)->insert($itemToInsert);
        }
    }
}
